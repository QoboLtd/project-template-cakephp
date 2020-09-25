<?php

namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\RepositoryInterface;
use Cake\ORM\Association;
use Cake\ORM\TableRegistry;
use DirectoryIterator;
use RuntimeException;

class Upgrade20180718130200Task extends Shell
{
    /**
     * @var string $dbConnection Connection name
     */
    private $dbConnection = 'default';

    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->setDescription('Add foreign key constraints to all tables');

        return $parser;
    }

    /**
     * Main method.
     *
     * @return int|bool|null
     */
    public function main()
    {
        if (! $this->needToRun()) {
            return false;
        }

        foreach ($this->getTables() as $table) {
            $this->processTable($table);
        }

        return true;
    }

    /**
     * Check if we can or should run.
     *
     * @return bool
     */
    private function needToRun(): bool
    {
        /**
         * @var \Cake\Database\Connection $connection
         */
        $connection = ConnectionManager::get($this->dbConnection);
        if (! $connection->getDriver() instanceof Mysql) {
            $this->warn('Skipping, not a MySQL database');

            return false;
        }

        $config = $connection->config();
        $generateForeignKeys = isset($config['generateForeignKeys']) ? (bool)$config['generateForeignKeys'] : false;
        if (! $generateForeignKeys) {
            $this->warn('Skipping, due to configuration');

            return false;
        }

        return true;
    }

    /**
     * Retrieve table instances.
     *
     * @return mixed[]
     */
    private function getTables(): array
    {
        $result = $this->getTablesFromPath(APP . 'Model' . DS . 'Table' . DS);
        /**
         * @var mixed[] $plugins
         */
        $plugins = Plugin::loaded();

        if (empty($plugins)) {
            return $result;
        }

        foreach ($plugins as $plugin) {
            $result = array_merge($result, $this->getTablesFromPath(
                Plugin::path($plugin) . 'src' . DS . 'Model' . DS . 'Table' . DS,
                $plugin
            ));
        }

        $joinTables = [];
        foreach ($result as $table) {
            $joinTables = array_merge($joinTables, $this->getJoinTables($table));
        }

        $result = array_merge($result, $joinTables);

        return $result;
    }

    /**
     * Method that retrieves table names from the provided path.
     *
     * @param string $path Directory path
     * @param string $plugin Plugin name
     * @return mixed[]
     */
    private function getTablesFromPath(string $path, string $plugin = ''): array
    {
        if (! file_exists($path)) {
            return [];
        }

        $result = [];
        foreach (new DirectoryIterator($path) as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }

            $basename = $fileInfo->getBasename('.php');

            if ('' !== trim($plugin)) {
                $basename = $plugin . '.' . $basename;
            }

            $className = App::className($basename, 'Model/Table');
            if (! $className) {
                continue;
            }

            $table = $this->getTableFromPath([
                'short_name' => App::shortName($className, 'Model/Table', 'Table'),
                'class_name' => $className,
            ]);

            if (is_null($table)) {
                continue;
            }

            array_push($result, $table);
        }

        return $result;
    }

    /**
     * Retrieve table class using path configuration.
     *
     * @param mixed[] $config Path configuration
     * @return \Cake\Datasource\RepositoryInterface|null
     */
    private function getTableFromPath(array $config): ?RepositoryInterface
    {
        $table = TableRegistry::getTableLocator()->get($config['short_name']);

        if ($table instanceof $config['class_name']) {
            return $table;
        }

        $this->warn(sprintf('Table "%s" is not an instance of "%s"', $table->getAlias(), $config['class_name']));

        return null;
    }

    /**
     * Retrieve join tables.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return mixed[]
     */
    private function getJoinTables(RepositoryInterface $table): array
    {
        $result = [];
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $table;
        foreach ($table->associations() as $association) {
            if ('manyToMany' !== $association->type()) {
                continue;
            }
            /**
             * @var \Cake\ORM\Association\BelongsToMany $association
             */
            $association = $association;
            array_push($result, $association->junction());
        }

        return $result;
    }

    /**
     * Process table.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return void
     */
    private function processTable(RepositoryInterface $table): void
    {
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $table;
        foreach ($table->associations() as $association) {
            if (! $this->isValidAssociation($association)) {
                continue;
            }

            $config = $this->processAssociation($association);

            if ($this->foreignKeyExists($table, $config)) {
                continue;
            }

            $this->addForeignKey($table, $config);
        }
    }

    /**
     * Association validator.
     *
     * @param \Cake\ORM\Association $association Association instance
     * @return bool
     */
    private function isValidAssociation(Association $association): bool
    {
        return in_array($association->type(), ['manyToOne']);
    }

    /**
     * Process association.
     *
     * @param \Cake\ORM\Association $association Association instance
     * @return mixed[]
     */
    private function processAssociation(Association $association): array
    {
        return [
            'table' => $association->getTarget()->getTable(),
            'primary_key' => $association->getTarget()->getPrimaryKey(),
            'foreign_key' => $association->getForeignKey(),
        ];
    }

    /**
     * Foreign key existance checker.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @param mixed[] $config Foreign key config
     * @return bool
     */
    private function foreignKeyExists(RepositoryInterface $table, array $config): bool
    {
        $foreignKeys = $this->getForeignKeysByTable($table);
        if (empty($foreignKeys)) {
            return false;
        }

        foreach ($foreignKeys as $foreignKey) {
            if (empty(array_diff_assoc($config, $foreignKey))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Table foreign keys getter.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return mixed[]
     */
    private function getForeignKeysByTable(RepositoryInterface $table): array
    {
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $table;
        $connection = ConnectionManager::get($this->dbConnection);
        $config = $connection->config();

        $query = $connection->newQuery();
        $query->select([
            'COLUMN_NAME as foreign_key',
            'REFERENCED_TABLE_NAME AS table',
            'REFERENCED_COLUMN_NAME AS primary_key',
        ]);
        $query->from('INFORMATION_SCHEMA.KEY_COLUMN_USAGE');
        $query->where(['REFERENCED_TABLE_SCHEMA' => $config['database'], 'TABLE_NAME' => $table->getTable()]);

        return $query->execute()->fetchAll('assoc');
    }

    /**
     * Foreign key creator.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table isntance
     * @param mixed[] $config Foreign key config
     * @return void
     */
    private function addForeignKey(RepositoryInterface $table, array $config): void
    {
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $table;
        /**
         * @var \Cake\Database\Connection $connection
         */
        $connection = ConnectionManager::get($this->dbConnection);
        $command = sprintf(
            'ALTER TABLE `%s` ADD FOREIGN KEY (`%s`) REFERENCES `%s`(`%s`)',
            $table->getTable(),
            $config['foreign_key'],
            $config['table'],
            $config['primary_key']
        );

        try {
            $connection->query($command);
            $this->success($command);
        } catch (\Exception $e) {
            $this->err(sprintf('Failed: %s', $command));
            $this->err(sprintf('Reason: %s', $e->getMessage()));
            throw new RuntimeException("Adding foreign key failed", 0, $e);
        }
    }
}
