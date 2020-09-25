<?php

namespace App\Shell\Task;

use App\Search\Manager;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Hash;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Qobo\Utils\Utility;

/**
 *  This class is responsible for creating system searches for all system Modules.
 */
class Upgrade20180404000000Task extends Shell
{
    /**
     * Configure option parser
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->setDescription('Create system searches for all system Modules');

        return $parser;
    }

    /**
     * Main method.
     *
     * @return int|bool|null
     */
    public function main()
    {
        if (! Plugin::loaded('Search')) {
            return false;
        }

        $path = Configure::readOrFail('CsvMigrations.modules.path');
        Utility::validatePath($path);
        $path = rtrim($path, DS);

        foreach (Utility::findDirs($path) as $module) {
            if (! $this->isSearchable($module)) {
                continue;
            }

            if (null !== Manager::getSystemSearch($module)) {
                continue;
            }

            Manager::createSystemSearch($module);
        }

        $this->success(sprintf('%s completed.', $this->getOptionParser()->getDescription()));

        return true;
    }

    /**
     * Validates if provided module is searchable.
     *
     * @param string $module Module name
     * @return bool
     */
    private function isSearchable(string $module): bool
    {
        $migrationConfig = new ModuleConfig(ConfigType::MIGRATION(), $module, null, ['cacheSkip' => true]);
        $config = $migrationConfig->parseToArray();

        if (empty($config)) {
            return false;
        }

        $moduleConfig = new ModuleConfig(ConfigType::MODULE(), $module, null, ['cacheSkip' => true]);
        $config = $moduleConfig->parseToArray();

        if ('module' !== Hash::get($config, 'table.type')) {
            return false;
        }

        return true;
    }
}
