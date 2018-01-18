<?php
use CsvMigrations\CsvMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class ScheduledJobs20180110123654 extends CsvMigration
{
        public function change()
    {
        $table = $this->table('scheduled_jobs', ['id' => false, 'primary_key' => ['id']]);

        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('name', 'string', [
            'default' => null,
            'null' => false,
        ]);

        $table->addColumn('job', 'string', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('options', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => true,
        ]);

        $table->addColumn('recurrence', 'text', [
            'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => true,
        ]);

        $table->addColumn('active', 'boolean', [
            'default' => 0,
            'null' => true,
        ]);

        $table->addColumn('priority', 'integer', [
            'default' => 0,
            'null' => true,
        ]);

        $table->addColumn('start_date', 'datetime', [
            'default' => null,
            'null' => true
        ]);

        $table->addColumn('end_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false
        ]);

        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false
        ]);

        $table->addColumn('created_by', 'uuid', [
            'default' => null,
            'null' => true
        ]);

        $table->addColumn('modified_by', 'uuid', [
            'default' => null,
            'null' => true,
        ]);

        $table->addColumn('trashed', 'datetime', [
            'default' => null,
            'null' => true,
        ]);

        $table->addIndex(['created_by'], [
            'name' => 'lookup_created_by'
        ]);

        $table->addIndex(['modified_by'], [
            'name' => 'lookup_modified_by',
        ]);

        $table->addPrimaryKey(['id']);

        $table->create();
    }
}
