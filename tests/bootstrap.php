<?php

/**
 * Test runner bootstrap.
 *
 * Add additional configuration/setup your application needs when running
 * unit tests in this file.
 */

use Cake\Console\ConsoleErrorHandler;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Mailer\TransportFactory;
use Cake\Mailer\Transport\DebugTransport;

require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/config/bootstrap.php';

$_SERVER['PHP_SELF'] = '/';

// reset logging configuration
array_map([Log::class, 'drop'], Log::configured());

// set logging configuration to file
Log::setConfig([
    'debug' => [
        'engine' => \Cake\Log\Engine\FileLog::class,
        'levels' => ['notice', 'info', 'debug'],
        'file' => 'debug',
        'path' => LOGS,
    ],
    'error' => [
        'engine' => \Cake\Log\Engine\FileLog::class,
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        'file' => 'error',
        'path' => LOGS,
    ],
]);

restore_error_handler();
// re-enable deprecation errors to be shown on the CLI during PHPunit execution.
Configure::write('Error.errorLevel', E_ALL);
// re-register application error and exception handlers.
(new ConsoleErrorHandler(Configure::read('Error')))->register();

$config = TransportFactory::getConfig('default');
$config['className'] = DebugTransport::class;

TransportFactory::drop('default');
TransportFactory::setConfig('default', $config);

// Enable table validations in tests
Configure::write('CsvMigrations.tableValidation', true);
