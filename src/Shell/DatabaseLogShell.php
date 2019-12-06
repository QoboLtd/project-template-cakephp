<?php

namespace App\Shell;

use Cake\Core\Configure;
use Cake\I18n\Time;
use DatabaseLog\Shell\DatabaseLogShell as BaseShell;

class DatabaseLogShell extends BaseShell
{
    /**
     * Deletes log records older than specified time (maxLength).
     *
     * @return void
     */
    public function gc(): void
    {
        $age = Configure::read('DatabaseLog.maxLength');
        if (!$age) {
            $this->info('Required parameter "maxLength" is not defined (garbage collector)');

            return;
        }

        $date = new Time($age);

        $count = $this->DatabaseLogs->deleteAll(['created <' => $date]);

        $this->info('Removed ' . number_format($count) . ' log records older than ' . ltrim($age, '-') . '.');
    }

    /**
     * Report stats/summary for logs
     *
     * @return void
     */
    public function stats(): void
    {
        $logLevels = $this->getLogLevels();
        $statsConfig = $this->getStatsConfig();

        $since = new Time($statsConfig['period']);
        $limit = $statsConfig['limit'];

        foreach ($logLevels as $logLevel) {
            $logs = $this->getLogStats($logLevel, $since, $limit);
            if ($logs->count() <= 0) {
                continue;
            }

            $this->info("Log level: $logLevel");
            foreach ($logs->toArray() as $result) {
                $count = number_format($result['count']);
                // Thanks to: https://stackoverflow.com/a/9097959/151647
                $message = strtok($result['message'], "\n");
                // NOTE: Log grouping in the database is done based on full log
                //       message, while the printout shows only the first line.
                //       This might sometimes cause the printout of identical
                //       messages as separate entries with different counts.
                //       If there is a reliable way to group by the first line
                //       of the log message in database, then this can be easily
                //       fixed.
                $this->out(sprintf("%5s : %s\n", $count, $message));
            }
            $this->hr();
        }
    }

    /**
     * Get stats log configuration
     *
     * @return mixed[]
     */
    protected function getStatsConfig(): array
    {
        $defaultConfig = [
            'period' => '-1 day',
            'limit' => 10,
        ];
        $statsConfig = Configure::read('DatabaseLog.stats');
        $result = is_array($statsConfig) ? array_merge($defaultConfig, $statsConfig) : $defaultConfig;

        if (isset($this->params['since'])) {
            $result['period'] = $this->params['since'];
        }

        return $result;
    }

    /**
     * Get the list of log levels
     *
     * @return string[]
     */
    protected function getLogLevels(): array
    {
        $result = [];

        $logLevels = Configure::read('DatabaseLog.typeStyles');
        if (empty($logLevels) || !is_array($logLevels)) {
            $this->abort("Missing log levels configuration");
        }

        $result = array_keys($logLevels);

        return $result;
    }

    /**
     * Get log stats for a given level
     *
     * @param string $logLevel Log level (e.g.: error, info, debug)
     * @param \Cake\I18n\Time $since Time limit (e.g.: -1 day)
     * @param int $limit Records limit
     * @return \Cake\ORM\Query
     */
    protected function getLogStats(string $logLevel, Time $since, int $limit): \Cake\ORM\Query
    {
        $query = $this->DatabaseLogs->find();
        $query = $query->select([
            'count' => $query->func()->count('*'),
            'message',
        ]);
        $query = $query->where([
            'type' => $logLevel,
            'created >= ' => $since
        ]);
        $query = $query->group(['message']);
        $query = $query->order(['count' => 'DESC']);
        $query = $query->limit($limit);

        return $query;
    }

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('gc', [
            'help' => 'Garbage collector.',
        ]);
        $parser->addSubcommand('stats', [
            'help' => 'Show log stats',
            'parser' => [
                'options' => [
                    'since' => [
                        'help' => __d('Qobo/ProjectTemplateCakephp', 'Period to be exported. Example "-1 day"'),
                        'required' => false,
                    ]
                ]
            ],
        ]);

        return $parser;
    }
}
