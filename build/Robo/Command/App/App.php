<?php

namespace Qobo\Robo\Command\App;

use Qobo\Robo\AbstractCommand;
use Qobo\Robo\Runner;

class App extends AbstractCommand
{

    /**
     * @var array $defaultEnv Default values if missing in env
     */
    protected $defaultEnv = [
        'CHMOD_FILE_MODE' => '0664',
        'CHMOD_DIR_MODE' => '02775',
    ];

    /**
     * Install a project
     *
     * @param string $env Custom env in KEY1=VALUE1,KEY2=VALUE2 format
     * @param array $options Command options
     * @option $skip-test-db Skip test database migrations
     * @return bool true on success or false on failure
     */
    public function appInstall($env = '', array $options = ['skip-test-db' => false])
    {
        $env = $this->getDotenv($env);

        if ($env === false || !$this->preInstall($env)) {
            $this->exitError("Failed to do pre-install ");
        }

        $result = $this->installCake($env, $options);

        if (!$result) {
            $this->exitError("Failed to do app:install");
        }

        if (isset($env['CRON_ENABLED']) && $env['CRON_ENABLED']) {
            $this->installCron($env);
        }

        return ($this->setPathPermissions($env) && $this->postInstall());
    }

    /**
     * Update a project
     *
     * @param string $env Custom env in KEY1=VALUE1,KEY2=VALUE2 format
     * @param array $options Command options
     * @option $skip-test-db Skip test database migrations
     * @return bool true on success or false on failure
     */
    public function appUpdate($env = '', array $options = ['skip-test-db' => false])
    {
        $env = $this->getDotenv($env);

        if ($env === false || !$this->preInstall($env)) {
            $this->exitError("Failed to do app:update");
        }

        $result = $this->updateCake($env, $options);

        if (!$result) {
            $this->exitError("Failed to do app:update");
        }

        if (isset($env['CRON_ENABLED']) && $env['CRON_ENABLED']) {
            $this->updateCron($env);
        }

        return ($this->setPathPermissions($env) && $this->postInstall());
    }

    /**
     * Remove a project
     *
     * @return bool true on success or false on failure
     */
    public function appRemove()
    {
        // clear all the caches
        $this->taskCakephpShellScript()->name('orm_cache')->param('clear')->run();
        $this->taskCakephpShellScript()->name('cache')->param('clear_all')->run();

        $env = $this->getDotenv();

        // drop test database
        $result = $this->taskMysqlDbDrop()
            ->db($this->getValue('DB_NAME', $env) . '_test')
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env))
            ->run();

        if (!$result->wasSuccessful()) {
            $this->exitError("Failed to do app:remove");
        }

        // drop project database
        $result = $this->taskMysqlDbDrop()
            ->db($this->getValue('DB_NAME', $env))
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env))
            ->run();

        if (!$result->wasSuccessful()) {
            $this->exitError("Failed to do app:remove");
        }

        // Remove .env
        if (!file_exists('.env') || !unlink('.env')) {
            $this->exitError("Failed to do app:remove");
        }

        $this->uninstallCron($env);

        return true;
    }

    /**
     * Do CakePHP related install things
     *
     * @param array $env Environment
     * @param array $options Command options
     * @return bool true on success or false on failure
     */
    protected function installCake($env, array $options)
    {
        // Check DB connectivity and get server time
        $result = $this->taskMysqlBaseQuery()
            ->query("SELECT NOW() AS ServerTime")
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env))
            ->run();

        if (!$result->wasSuccessful()) {
            return false;
        }
        $this->say(implode(": ", $result->getData()['data'][0]['output']));

        // prepare all remaining tasks in this array
        $tasks = [];

        // create DB
        $tasks[] = $this->taskMysqlDbCreate()
            ->db($this->getValue('DB_NAME', $env))
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env));

        // drop test DB
        $tasks[] = $this->taskMysqlDbDrop()
             ->db($this->getValue('DB_NAME', $env) . "_test")
             ->user($this->getValue('DB_ADMIN_USER', $env))
             ->pass($this->getValue('DB_ADMIN_PASS', $env))
             ->hide($this->getValue('DB_ADMIN_PASS', $env))
             ->host($this->getValue('DB_HOST', $env));

        // create test DB
        $tasks[] = $this->taskMysqlDbCreate()
            ->db($this->getValue('DB_NAME', $env) . "_test")
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env));

        // execute all tasks
        foreach ($tasks as $task) {
            $result = $task->run();
            if (!$result->wasSuccessful()) {
                return false;
            }
        }
        $tasks = [];

        // Execute migration for Database Logs first, so that it's available for logging
        $tasks[] = $this->taskCakephpMigration()
            ->connection('test')
            ->plugin('DatabaseLog');

        // Execute migration for Database Logs first, so that it's available for logging
        $tasks[] = $this->taskCakephpMigration()
            ->plugin('DatabaseLog');

        // Execute module generation without decorators so the migrations can pass.
        // We don't use cakeShellScript task here because it doesn't allow to pass multiple parameters.
        $tasks[] = $this->taskExec('./bin/cake generate_modules -q -f --skip-decorators');

        // get a list of cakephp plugins
        $result = $this->taskCakephpPlugins()->run();
        if (!$result->wasSuccessful()) {
            return false;
        }
        $plugins = $result->getData()['data'];

        if (! (bool)$options['skip-test-db']) {
            // test plugin migrations
            foreach ($plugins as $plugin) {
                $tasks[] = $this->taskCakephpMigration()
                    ->connection('test')
                    ->plugin($plugin);
            }

            // test app migration
            $tasks[] = $this->taskCakephpMigration()
                ->connection('test');

            // drop test DB
            $tasks[] = $this->taskMysqlDbDrop()
                 ->db($this->getValue('DB_NAME', $env) . "_test")
                 ->user($this->getValue('DB_ADMIN_USER', $env))
                 ->pass($this->getValue('DB_ADMIN_PASS', $env))
                 ->hide($this->getValue('DB_ADMIN_PASS', $env))
                 ->host($this->getValue('DB_HOST', $env));

            // create test DB
            $tasks[] = $this->taskMysqlDbCreate()
                ->db($this->getValue('DB_NAME', $env) . "_test")
                ->user($this->getValue('DB_ADMIN_USER', $env))
                ->pass($this->getValue('DB_ADMIN_PASS', $env))
                ->hide($this->getValue('DB_ADMIN_PASS', $env))
                ->host($this->getValue('DB_HOST', $env));
        }

        // cleanup database logs
        $tasks[] = $this->taskCakephpShellScript()->name('database_log')->param('gc');

        // do plugin migrations
        foreach ($plugins as $plugin) {
            $tasks[] = $this->taskCakephpMigration()
                ->plugin($plugin);
        }

        // do app migrations
        $tasks[] = $this->taskCakephpMigration();

        $tasks[] = $this->taskCakephpAdminAdd()
            ->username($this->getValue('DEV_USER', $env))
            ->password($this->getValue('DEV_PASS', $env))
            ->email($this->getValue('DEV_EMAIL', $env));

        $shellScripts = [
            'generate_modules -f',
            'upgrade',
            'group import',
            'group assign',
            'role import',
            'capability assign',
            'menu import',
            'validate', // run after dblists are populated
            'settings',
        ];

        foreach ($shellScripts as $script) {
            if (strstr($script, " ")) {
                list($name, $param) = explode(" ", $script);
                $tasks[] = $this->taskCakephpShellScript()->name($name)->param($param);
            } else {
                $tasks[] = $this->taskCakephpShellScript()->name($script);
            }
        }

        // clear cache as last task
        $tasks[] = $this->taskCakephpCacheClear();

        // execute all tasks
        foreach ($tasks as $task) {
            $result = $task->run();
            if (!$result->wasSuccessful()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Do CakePHP related update things
     *
     * @param array $env Environment
     * @param array $options Command options
     * @return bool true on success or false on failure
     */
    protected function updateCake($env, array $options)
    {
        // Check DB connectivity and get server time
        $result = $this->taskMysqlBaseQuery()
            ->query("SELECT NOW() AS ServerTime")
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env))
            ->run();

        if (!$result->wasSuccessful()) {
            return false;
        }
        $this->say(implode(": ", $result->getData()['data'][0]['output']));

        // prepare all remaining tasks in this array
        $tasks = [];

        // clear cache as first task
        $tasks[] = $this->taskCakephpCacheClear();

        // drop test DB
        $tasks[] = $this->taskMysqlDbDrop()
             ->db($this->getValue('DB_NAME', $env) . "_test")
             ->user($this->getValue('DB_ADMIN_USER', $env))
             ->pass($this->getValue('DB_ADMIN_PASS', $env))
             ->hide($this->getValue('DB_ADMIN_PASS', $env))
             ->host($this->getValue('DB_HOST', $env));

        // create test DB
        $tasks[] = $this->taskMysqlDbCreate()
            ->db($this->getValue('DB_NAME', $env) . "_test")
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env));

        // execute all tasks
        foreach ($tasks as $task) {
            $result = $task->run();
            if (!$result->wasSuccessful()) {
                return false;
            }
        }
        $tasks = [];

        // Execute migration for Database Logs first, so that it's available for logging
        $tasks[] = $this->taskCakephpMigration()
            ->connection('test')
            ->plugin('DatabaseLog');

        // get a list of cakephp plugins
        $result = $this->taskCakephpPlugins()->run();
        if (!$result->wasSuccessful()) {
            return false;
        }
        $plugins = $result->getData()['data'];

        if (! (bool)$options['skip-test-db']) {
            // test plugin migrations
            foreach ($plugins as $plugin) {
                $tasks[] = $this->taskCakephpMigration()
                    ->connection('test')
                    ->plugin($plugin);
            }

            // test app migration
            $tasks[] = $this->taskCakephpMigration()
                ->connection('test');

            // drop test DB
            $tasks[] = $this->taskMysqlDbDrop()
                 ->db($this->getValue('DB_NAME', $env) . "_test")
                 ->user($this->getValue('DB_ADMIN_USER', $env))
                 ->pass($this->getValue('DB_ADMIN_PASS', $env))
                 ->hide($this->getValue('DB_ADMIN_PASS', $env))
                 ->host($this->getValue('DB_HOST', $env));

            // create test DB
            $tasks[] = $this->taskMysqlDbCreate()
                ->db($this->getValue('DB_NAME', $env) . "_test")
                ->user($this->getValue('DB_ADMIN_USER', $env))
                ->pass($this->getValue('DB_ADMIN_PASS', $env))
                ->hide($this->getValue('DB_ADMIN_PASS', $env))
                ->host($this->getValue('DB_HOST', $env));
        }

        // Execute migration for Database Logs first, so that it's available for logging
        $tasks[] = $this->taskCakephpMigration()
            ->plugin('DatabaseLog');

        // cleanup database logs
        $tasks[] = $this->taskCakephpShellScript()->name('database_log')->param('gc');

        // do plugin migrations
        foreach ($plugins as $plugin) {
            $tasks[] = $this->taskCakephpMigration()
                ->plugin($plugin);
        }

        // do app migrations
        $tasks[] = $this->taskCakephpMigration();

        $shellScripts = [
            'generate_modules -f',
            'upgrade',
            'group import',
            'group assign',
            'role import',
            'capability assign',
            'menu import',
            'validate', // run after dblists are populated
            'settings',
        ];

        foreach ($shellScripts as $script) {
            if (strstr($script, " ")) {
                list($name, $param) = explode(" ", $script);
                $tasks[] = $this->taskCakephpShellScript()->name($name)->param($param);
            } else {
                $tasks[] = $this->taskCakephpShellScript()->name($script);
            }
        }

        // clear cache as last task
        $tasks[] = $this->taskCakephpCacheClear();

        // execute all tasks
        foreach ($tasks as $task) {
            $result = $task->run();
            if (!$result->wasSuccessful()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Recreates and reloads environment
     *
     * @param string $env Custom env in KEY1=VALUE1,KEY2=VALUE2 format
     *
     * @return mixed Env array or false on failure
     */
    protected function getDotenv($env = '')
    {
        $batch = $this->collectionBuilder();

        $task = $batch->taskProjectDotenvCreate()
            ->env('.env')
            ->template('.env.example');

        $vars = explode(',', $env);
        foreach ($vars as $var) {
            $var = trim($var);
            if (preg_match('/^(.*?)=(.*?)$/', $var, $matches)) {
                $task->set($matches[1], $matches[2]);
            }
        }

        $result = $task->taskDotenvReload()
                ->path('.env')
            ->run();

        if (!$result->wasSuccessful()) {
            return false;
        }

        $env = $result->getData()['data'];
        foreach ($this->defaultEnv as $k => $v) {
            if (!array_key_exists($k, $env)) {
                $env[$k] = $v;
            }
        }

        return $env;
    }

    /**
     * Find a value for configuration parameter
     *
     * @param string $name Parameter name
     * @param array $env Environment
     *
     * @return string
     */
    protected function getValue($name, $env)
    {
        // try to match in given $env
        if (!empty($env) && isset($env[$name])) {
            return $env[$name];
        }

        // look in real ENV
        $value = getenv($name);
        if ($value !== false) {
            return $value;
        }

        // look in the defaults
        if (!empty($this->defaultEnv) && isset($this->defaultEnv[$name])) {
            return $this->defaultEnv[$name];
        }

        // return null if nothing
        return null;
    }

    /**
     * Pre-install
     *
     * @param array $env Environment
     * @return bool
     */
    protected function preInstall($env)
    {
        // old :builder:init
        if (!$this->versionBackup("build/version")) {
            return false;
        }

        // old :file:process
        return $this->taskTemplateProcess()
            ->wrap('%%')
            ->tokens($env)
            ->src(getenv('TEMPLATE_SRC'))
            ->dst(getenv('TEMPLATE_DST'))
            ->run()
            ->wasSuccessful();
    }

    /**
     * Post-install
     *
     * @return bool
     */
    protected function postInstall()
    {
        return $this->versionBackup("build/version.ok");
    }

    /**
     * Backup version file
     *
     * @param string $path Path to version file
     * @return bool
     */
    protected function versionBackup($path)
    {
        $projectVersion = $this->getProjectVersion();
        if (file_exists($path)) {
            rename($path, "$path.bak");
        }

        return (file_put_contents($path, $projectVersion) === false) ? false : true;
    }

    /**
     * Get project version
     *
     * @return string
     */
    protected function getProjectVersion()
    {
        $envVersion = getenv('GIT_BRANCH');
        if (!empty($envVersion)) {
            return $envVersion;
        }

        $result = $this->taskGitHash()->run();
        if ($result->wasSuccessful()) {
            return $result->getData()['data'][0]['message'];
        }

        return "Unknown";
    }

    /**
     * Install system cron job for the project
     *
     * @param array $env Environment
     * @return void
     */
    protected function installCron($env)
    {
        $projectPath = "{$env['NGINX_ROOT_PREFIX']}/{$env['NGINX_SITE_MAIN']}";

        if (! self::cronShellExists($projectPath) || self::cronFileExists($env)) {
            return;
        }

        if (!is_dir("$projectPath/logs")) {
            $this->taskExec('mkdir ' . $projectPath . '/logs')->run();
        }
        $redirectPath = ((bool)$env['CRON_LOG_ENABLED']) ? $projectPath . '/logs/cron.log' : '/dev/null';
        $this->taskExec('echo "* * * * * root ' . $projectPath . '/bin/cron.sh >> ' . $redirectPath . ' 2>&1" > /etc/cron.d/' . $env['NGINX_SITE_MAIN'])->run();
        $this->taskExec('service crond reload')->run();
    }

    /**
     * Update system cron job for the project
     *
     * @param array $env Environment
     * @return void
     */
    protected function updateCron($env)
    {
        $projectPath = "{$env['NGINX_ROOT_PREFIX']}/{$env['NGINX_SITE_MAIN']}";

         if (! self::cronShellExists($projectPath) || ! self::cronFileExists($env)) {
            return;
        }

        $redirectPath = ((bool)$env['CRON_LOG_ENABLED']) ? $projectPath . '/logs/cron.log' : '/dev/null';
        $this->taskExec('echo "* * * * * root ' . $projectPath . '/bin/cron.sh >> ' . $redirectPath . ' 2>&1" > /etc/cron.d/' . $env['NGINX_SITE_MAIN'])->run();
        $this->taskExec('service crond reload')->run();
    }

    /**
     * Uninstall system cron job for the project
     *
     * @param array $env Environment
     * @return void
     */
    protected function uninstallCron($env)
    {
        if (! self::cronFileExists($env)) {
            return;
        }
        $this->taskExec("rm -f '/etc/cron.d/{$env['NGINX_SITE_MAIN']}'")->run();
    }

    /**
     * Verifies that cron file exists.
     *
     * @param array $env Environment
     * @return bool
     */
    private static function cronFileExists($env): bool
    {
        return file_exists("/etc/cron.d/{$env['NGINX_SITE_MAIN']}");
    }

    /**
     * Verifies that cron shell exists.
     *
     * @param string $projectPath Project path
     * @return bool
     */
    private static function cronShellExists($projectPath): bool
    {
        return file_exists("$projectPath/bin/cron.sh");
    }

    /**
     * Set correct paths permissions and ownerships
     *
     * @param array $env Environment
     * @return bool
     */
    protected function setPathPermissions($env)
    {
        $lastError = Runner::getLastError();

        $dirMode = $this->getValue('CHMOD_DIR_MODE', $env);
        $fileMode = $this->getValue('CHMOD_FILE_MODE', $env);

        $chmodPaths = array_filter(explode(",", $this->getValue('CHMOD_PATH', $env)));
        $chownPaths = array_filter(explode(",", $this->getValue('CHOWN_PATH', $env)));
        $chgrpPaths = array_filter(explode(",", $this->getValue('CHGRP_PATH', $env)));

        $user = $this->getValue('CHOWN_USER', $env);
        $group = $this->getValue('CHGRP_GROUP', $env);

        $base = str_replace("build/Robo/Command/App", "", __DIR__);

        $tasks = [];

        if (!empty($fileMode) && !empty($dirMode)) {
            foreach ($chmodPaths as $path) {
                if (!file_exists("$base$path")) {
                    continue;
                }

                // Chmod dir
                $tasks[] = $this->taskFileChmod()
                    ->path("$base$path")
                    ->fileMode($fileMode)
                    ->dirMode($dirMode)
                    ->recursive(true);
            }
        }

        if (!empty($user)) {
            foreach ($chownPaths as $path) {
                if (!file_exists("$base$path")) {
                    continue;
                }

                // Chown dir
                $tasks[] = $this->taskFileChown()
                    ->path("$base$path")
                    ->user($user)
                    ->recursive(true);
            }
        }

        if (!empty($group)) {
            foreach ($chgrpPaths as $path) {
                if (!file_exists("$base$path")) {
                    continue;
                }

                // Chgrp dir
                $tasks[] = $this->taskFileChgrp()
                    ->path("$base$path")
                    ->group($group)
                    ->recursive(true);
            }
        }

        // execute all tasks
        foreach ($tasks as $task) {
            $error = false;
            try {
                $result = $task->run();
                if (!$result->wasSuccessful()) {
                    $error = true;
                    print "Failed to run task\n";
                }
            } catch (\Exception $e) {
                print "{$e->getMessage()}\n";
                $error = true;
            }

            if ($error && !$this->getValue('IGNORE_FS_ERRORS', $env)) {
                return false;
            }
        }

        Runner::setLastError($lastError);

        return true;
    }
}
