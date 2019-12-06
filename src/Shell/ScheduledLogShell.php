<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use RuntimeException;

/**
 * ScheduledLogShell shell command.
 *
 * Delete
 *
 */
class ScheduledLogShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->setDescription('Sheduled job logs.');
        $parser->addSubcommand('gc', [
            'help' => 'Clean scheduled jog logs',
            'parser' => [
                'options' => [
                    'age' => [
                        'help' => __d('Qobo/ProjectTemplateCakephp', 'Time to delete (ex: -1 day / -1 months)'),
                        'required' => false,
                        'short' => 'a'
                    ]
                ]
            ],
        ]);

        return $parser;
    }

    /**
     * main() method.
     *
     * @return null Success or error code.
     */
    public function gc()
    {
        $age = $this->getDaysConfig();
        // Delete before this day
        $date = new Time($age);
        $query = TableRegistry::get('ScheduledJobLogs');
        // Count how many has been deleted
        $count = $query->deleteAll(['created <' => $date]);
        $this->info('Removed ' . number_format($count) . ' log records older than ' . ltrim($age, '-') . '.');
        // Write in the Log
        Log::write('info', "Clean up scheduled job logs older then $age.");

        return null;
    }

    /**
     * Get stats log configuration
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getDaysConfig(): string
    {
        if (isset($this->params['age'])) {
            return $this->params['age'];
        }

        $result = Configure::read('ScheduledLog.stats.age');
        if ($result) {
            return $result;
        }

        $this->info('Parameter "age" is not set in config/ScheduledLog');

        throw new RuntimeException('Parameter "age" is not set in config/ScheduledLog');
    }
}
