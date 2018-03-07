<?php

namespace Qobo\Robo\Command\App;

use \Qobo\Robo\AbstractCommand;

class App extends AbstractCommand
{

    /**
     * @var array $defaultEnv Default values if missing in env
     */
    protected $defaultEnv = [
        'CHMOD_FILE_MODE'   => '0664',
        'CHMOD_DIR_MODE'    => '02775'
    ];

    /**
     * Install a project
     *
     * @param string $env Custom env in KEY1=VALUE1,KEY2=VALUE2 format
     *
     * @return bool true on success or false on failure
     */
    public function appInstall($env = '')
    {
        $salt = $this->generateRandomString(64);
        file_put_contents('build/.salt', $salt);

        if (empty($env)) {
            $env = "SALT=$salt";
        } else {
            $env .= ",SALT=$salt";
        }

        $env = $this->getDotenv($env);

        if ($env === false || !$this->preInstall($env)) {
            $this->exitError("Failed to do pre-install ");
        }

        $result = $this->installCake($env);

        if (!$result) {
            $this->exitError("Failed to do app:install");
        }

        return $this->postInstall();
    }

    /**
     * Update a project
     *
     * @param string $env Custom env in KEY1=VALUE1,KEY2=VALUE2 format
     *
     * @return bool true on success or false on failure
     */
    public function appUpdate($env = '')
    {
        if (file_exists('build/.salt')) {
            $salt = file_get_contents('build/.salt');
            if (empty($env)) {
                $env = "SALT=$salt";
            } else {
                $env .= ",SALT=$salt";
            }
        }
        $env = $this->getDotenv($env);

        if ($env === false || !$this->preInstall($env)) {
            $this->exitError("Failed to do app:update");
        }

        $result = $this->updateCake($env);

        if (!$result) {
            $this->exitError("Failed to do app:update");
        }

        return $this->postInstall();
    }

    /**
     * Remove a project
     *
     * @return bool true on success or false on failure
     */
    public function appRemove()
    {
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

        return true;
    }

    /**
     * Do CakePHP related install things
     *
     * @return bool true on success or false on failure
     */
    protected function installCake($env)
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
        $tasks []= $this->taskMysqlDbCreate()
            ->db($this->getValue('DB_NAME', $env))
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env));


        // drop test DB
        $tasks []= $this->taskMysqlDbDrop()
             ->db($this->getValue('DB_NAME', $env) . "_test")
             ->user($this->getValue('DB_ADMIN_USER', $env))
             ->pass($this->getValue('DB_ADMIN_PASS', $env))
             ->hide($this->getValue('DB_ADMIN_PASS', $env))
             ->host($this->getValue('DB_HOST', $env));

        // create test DB
        $tasks []= $this->taskMysqlDbCreate()
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

        // get a list of cakephp plugins
        $result = $this->taskCakephpPlugins()->run();
        if (!$result->wasSuccessful()) {
            return false;
        }
        $plugins = $result->getData()['data'];

        // test plugin migrations
        foreach ($plugins as $plugin) {
            $tasks []= $this->taskCakephpMigration()
                ->connection('test')
                ->plugin($plugin);
        }

        // test app migration
        $tasks []= $this->taskCakephpMigration()
            ->connection('test');

        // drop test DB
        $tasks []= $this->taskMysqlDbDrop()
             ->db($this->getValue('DB_NAME', $env) . "_test")
             ->user($this->getValue('DB_ADMIN_USER', $env))
             ->pass($this->getValue('DB_ADMIN_PASS', $env))
             ->hide($this->getValue('DB_ADMIN_PASS', $env))
             ->host($this->getValue('DB_HOST', $env));

        // create test DB
        $tasks []= $this->taskMysqlDbCreate()
            ->db($this->getValue('DB_NAME', $env) . "_test")
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env));

        // cleanup database logs
        $tasks []= $this->taskCakephpShellScript()->name('database_log')->param('gc');

        // do plugin migrations
        foreach ($plugins as $plugin) {
            $tasks []= $this->taskCakephpMigration()
                ->plugin($plugin);
        }

        // do app migrations
        $tasks []= $this->taskCakephpMigration();

        $tasks []= $this->taskCakephpAdminAdd()
            ->username($this->getValue('DEV_USER', $env))
            ->password($this->getValue('DEV_PASS', $env))
            ->email($this->getValue('DEV_EMAIL', $env));

        $shellScripts = [
            'upgrade',
            'group import',
            'group assign',
            'role import',
            'capability assign',
            'menu import',
            'add_dblist_permissions',
            'dblists_add',
            'validate' // run after dblists are populated
        ];

        foreach ($shellScripts as $script) {
            if (strstr($script, " ")) {
                list($name, $param) = explode(" ", $script);
                $tasks []= $this->taskCakephpShellScript()->name($name)->param($param);
            } else {
                $tasks []= $this->taskCakephpShellScript()->name($script);
            }
        }

        $paths = [
            'tmp',
            'logs',
            'webroot/uploads'
        ];
        $dirMode = $this->getValue('CHMOD_DIR_MODE', $env);
        $fileMode = $this->getValue('CHMOD_FILE_MODE', $env);
        $user = $this->getValue('CHOWN_USER', $env);
        $group = $this->getValue('CHGRP_GROUP', $env);

        foreach ($paths as $path) {

            $path = str_replace("build/Robo/Command/App", "",  __DIR__) . $path;
            if (!file_exists($path)) {
                continue;
            }

            // Chmod dir
            $tasks []= $this->taskFileChmod()
                ->path([$path])
                ->fileMode($fileMode)
                ->dirMode($dirMode)
                ->recursive(true);

            // Chown dir
            $tasks []= $this->taskFileChown()
                ->path([$path])
                ->user($user)
                ->recursive(true);

            // Chgrp dir
            $tasks []= $this->taskFileChgrp()
                ->path([$path])
                ->group($group)
                ->recursive(true);
        }

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
     * @return bool true on success or false on failure
     */
    protected function updateCake($env)
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

        // drop test DB
        $tasks []= $this->taskMysqlDbDrop()
             ->db($this->getValue('DB_NAME', $env) . "_test")
             ->user($this->getValue('DB_ADMIN_USER', $env))
             ->pass($this->getValue('DB_ADMIN_PASS', $env))
             ->hide($this->getValue('DB_ADMIN_PASS', $env))
             ->host($this->getValue('DB_HOST', $env));

        // create test DB
        $tasks []= $this->taskMysqlDbCreate()
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

        // get a list of cakephp plugins
        $result = $this->taskCakephpPlugins()->run();
        if (!$result->wasSuccessful()) {
            return false;
        }
        $plugins = $result->getData()['data'];

        // test plugin migrations
        foreach ($plugins as $plugin) {
            $tasks []= $this->taskCakephpMigration()
                ->connection('test')
                ->plugin($plugin);
        }

        // test app migration
        $tasks []= $this->taskCakephpMigration()
            ->connection('test');

        // drop test DB
        $tasks []= $this->taskMysqlDbDrop()
             ->db($this->getValue('DB_NAME', $env) . "_test")
             ->user($this->getValue('DB_ADMIN_USER', $env))
             ->pass($this->getValue('DB_ADMIN_PASS', $env))
             ->hide($this->getValue('DB_ADMIN_PASS', $env))
             ->host($this->getValue('DB_HOST', $env));

        // create test DB
        $tasks []= $this->taskMysqlDbCreate()
            ->db($this->getValue('DB_NAME', $env) . "_test")
            ->user($this->getValue('DB_ADMIN_USER', $env))
            ->pass($this->getValue('DB_ADMIN_PASS', $env))
            ->hide($this->getValue('DB_ADMIN_PASS', $env))
            ->host($this->getValue('DB_HOST', $env));

        $tasks [] = $this->taskCakephpCacheClear();

        // cleanup database logs
        $tasks []= $this->taskCakephpShellScript()->name('database_log')->param('gc');

        // do plugin migrations
        foreach ($plugins as $plugin) {
            $tasks []= $this->taskCakephpMigration()
                ->plugin($plugin);
        }

        // do app migrations
        $tasks []= $this->taskCakephpMigration();

        $shellScripts = [
            'upgrade',
            'group import',
            'group assign',
            'role import',
            'capability assign',
            'menu import',
            'add_dblist_permissions',
            'dblists_add',
            'validate' // run after dblists are populated
        ];

        foreach ($shellScripts as $script) {
            if (strstr($script, " ")) {
                list($name, $param) = explode(" ", $script);
                $tasks []= $this->taskCakephpShellScript()->name($name)->param($param);
            } else {
                $tasks []= $this->taskCakephpShellScript()->name($script);
            }
        }

        $paths = [
            'tmp',
            'logs',
            'webroot/uploads'
        ];
        $dirMode = $this->getValue('CHMOD_DIR_MODE', $env);
        $fileMode = $this->getValue('CHMOD_FILE_MODE', $env);
        $user = $this->getValue('CHOWN_USER', $env);
        $group = $this->getValue('CHGRP_GROUP', $env);

        foreach ($paths as $path) {

            $path = str_replace("build/Robo/Command/App", "",  __DIR__) . $path;
            if (!file_exists($path)) {
                continue;
            }

            // Chmod dir
            $tasks []= $this->taskFileChmod()
                ->path([$path])
                ->fileMode($fileMode)
                ->dirMode($dirMode)
                ->recursive(true);

            // Chown dir
            $tasks []= $this->taskFileChown()
                ->path([$path])
                ->user($user)
                ->recursive(true);

            // Chgrp dir
            $tasks []= $this->taskFileChgrp()
                ->path([$path])
                ->group($group)
                ->recursive(true);
        }

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

    protected function postInstall()
    {
        return $this->versionBackup("build/version.ok");
    }

    protected function versionBackup($path)
    {
        $projectVersion = $this->getProjectVersion();
        if (file_exists($path)) {
            rename($path, "$path.bak");
        }
        return (file_put_contents($path, $projectVersion) === false) ? false : true;
    }

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

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
