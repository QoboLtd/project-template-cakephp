<?php
namespace App\ScheduledJobs\Handlers;

use App\ScheduledJobs\Handlers\AbstractHandler;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;

class CakeShellHandler extends AbstractHandler
{
    /** @var $prefix for differentiating jobs */
    protected $prefix = 'CakeShell';

    /**
     * Get List of Shells
     *
     * Code is pretty much taken from CommandsShell of CakePHP core
     *
     * @param array $options with configs passed if any
     *
     * @return array $result with associated array of plugins and its commands.
     */
    public function getList(array $options = [])
    {
        $result = [];

        $skipFiles = ['ConsoleShell', 'FakerShell', 'PluginShell'];
        $skipPlugins = ['Bake'];

        $plugins = Plugin::loaded();
        $plugins = array_diff($plugins, $skipPlugins);

        $shellList = array_fill_keys($plugins, null) + ['CORE' => null, 'app' => null];

        $appPath = App::path('Shell');
        $appShells = $this->scanDir($appPath[0]);
        $appShells = array_diff($appShells, $skipFiles);

        $shellList = $this->appendShells('app', $appShells, $shellList);

        foreach ($plugins as $plugin) {
            $pluginPath = Plugin::classPath($plugin) . 'Shell';
            $pluginShells = $this->scanDir($pluginPath);
            $shellList = $this->appendShells($plugin, $pluginShells, $shellList);
        }

        $shellList = array_filter($shellList);

        // flatting command list
        foreach ($shellList as $plugin => $shells) {
            foreach ($shells as $name) {
                $result[] = $this->prefix . '::' . ucfirst($plugin) . ':' . $name;
            }
        }

        // sorting shells alphabetically
        asort($result);

        // fixing array indexing
        $result = array_values($result);

        return $result;
    }

    /**
     * Scan the provided paths for shells, and append them into $shellList
     *
     * @param string $type The type of object.
     * @param array $shells The shell name.
     * @param array $shellList List of shells.
     * @return array The updated $shellList
     */
    protected function appendShells($type, $shells, $shellList)
    {
        foreach ($shells as $shell) {
            $shellList[$type][] = Inflector::underscore(str_replace('Shell', '', $shell));
        }

        return $shellList;
    }

    /**
     * Scan a directory for .php files and return the class names that
     * should be within them.
     *
     * @param string $dir The directory to read.
     * @return array The list of shell classnames based on conventions.
     */
    protected function scanDir($dir)
    {
        $dir = new Folder($dir);
        $contents = $dir->read(true, true);
        if (empty($contents[1])) {
            return [];
        }
        $shells = [];
        foreach ($contents[1] as $file) {
            if (substr($file, -4) !== '.php') {
                continue;
            }
            $shells[] = substr($file, 0, -4);
        }

        return $shells;
    }
}
