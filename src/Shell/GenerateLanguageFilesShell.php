<?php

namespace App\Shell;

use CakeDC\Users\Shell\UsersShell as BaseShell;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use CsvMigrations\Controller\Traits\PanelsTrait;
use CsvMigrations\Utility as CsvUtility;
use CsvMigrations\Utility\Validate\Utility;
use PHPUnit\Framework\Assert;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Qobo\Utils\ModuleConfig\Parser\Parser;

class GenerateLanguageFilesShell extends BaseShell
{
    use PanelsTrait;

    /**
     * @var array $modules List of known modules
     */
    protected $modules;
    /**
     * @var string
     */
    private $module;

    /**
     * Set shell description and command line options
     *
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = new ConsoleOptionParser('console');
        $parser->setDescription('Generating .ctp files for language translation');
        $parser->addOption('module', [
            'short' => 'm',
            'help' => 'Specific module to fix',
        ]);

        return $parser;
    }

    /**
     * Main method for shell execution
     *
     * @param string $modules Comma-separated list of module names to validate
     * @return bool|int|void
     */
    public function main(string $modules = '')
    {
        $this->setModule();
        $this->modules = !empty($this->getModule()) ? (array)$this->getModule() : Utility::getModules();

        if (empty($this->modules)) {
            $this->warn('Did not find any modules');

            return false;
        }

        $modules = '' === $modules ? $this->modules : explode(',', $modules);

        foreach ($modules as $module) {
            $this->generateFiles((string)$module);
        }
    }

    /**
     * Generate file for each module.
     * @param string $module Module name
     * @return void
     */
    public function generateFiles(string $module): void
    {
        $ctpLines = "<?php\n\n";

        //Module Title
        $ctpLines .= $this->generateModuleTitleLine($module);
        //Module Alias
        $ctpLines .= $this->generateModuleAliasLine($module);
        //Field Labels
        $ctpLines .= $this->generateModuleFieldLabelLines($module);
        //Migration Field Labels
        $ctpLines .= $this->generateModuleMigrationFieldLabels($module);
        //Menu Labels
        $ctpLines .= $this->generateModuleMenuLabels($module);
        //Panel Title labels
        $ctpLines .= $this->generatePanelTitleLabels($module);

        $filename = 'src/Template/Module/Translations/' . $module . ".ctp";
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        Assert::assertIsWritable(dirname($filename), (string)__("Unable to open file {0}.ctp", $module));
        if (false !== $ctpFile = fopen($filename, "w")) {
            $ctpContent = $ctpLines . "\n";
            fwrite($ctpFile, $ctpContent);
            fclose($ctpFile);
        }
    }

    /**
     * Generate lines for table title.
     * @param string $module Module name
     * @return string
     */
    private function generateModuleTitleLine(string $module): string
    {
        $ctpLines = '';
        $ctpLines .= $this->generateCommentLine("Module Title") . "\n";
        $ctpLines .= $this->generateLine(Inflector::humanize(Inflector::underscore($module))) . "\n\n";

        return $ctpLines;
    }

    /**
     * Generate lines for table alias.
     * @param string $module Module name
     * @return string
     */
    private function generateModuleAliasLine(string $module): string
    {
        $mc = $this->getModuleConfig($module, []);
        $configFile = (string)$mc->find(false);
        $moduleAlias = $mc->parseToArray();

        $ctpLines = '';
        $ctpLines .= $this->generateCommentLine(str_replace(CONFIG, 'config' . DS, $configFile)) . "\n";
        $ctpLines .= $this->generateCommentLine("Module Alias") . "\n";
        $ctpLines .= $this->generateLine(isset($moduleAlias['table']['alias']) ? $moduleAlias['table']['alias'] : Inflector::humanize(Inflector::underscore($module))) . "\n\n";

        return $ctpLines;
    }

    /**
     * Generate lines for field labels.
     * @param string $module Module name
     * @return string
     */
    private function generateModuleFieldLabelLines(string $module): string
    {
        $fieldLabelConfig = (new ModuleConfig(ConfigType::FIELDS(), $module));
        $fieldsFile = (string)$fieldLabelConfig->find(false);

        $ctpLines = '';

        if (!empty($fieldLabelConfig->parseToArray())) {
            $ctpLines .= $this->generateCommentLine(str_replace(CONFIG, 'config' . DS, $fieldsFile)) . "\n";
        }
        foreach ($fieldLabelConfig->parseToArray() as $key => $fieldLabel) {
            if (isset($fieldLabel['label'])) {
                $ctpLines .= $this->generateLine($fieldLabel['label']) . "\n\n";
            }

            if (isset($fieldLabel['help'])) {
                $ctpLines .= $this->generateLine($fieldLabel['help']) . "\n\n";
            }
        }

        return $ctpLines;
    }

    /**
     * Generate lines for migration field labels.
     * @param string $module Module name
     * @return string
     */
    private function generateModuleMigrationFieldLabels(string $module): string
    {
        $ctpLines = '';

        $migrationFieldLabels = $this->migrationFieldLabels($module);
        if (is_array($migrationFieldLabels) && 0 < count($migrationFieldLabels)) {
            $ctpLinesArray = [];
            $ctpLinesArray = array_map([$this, "generateLine"], $migrationFieldLabels);
            $ctpLines .= $this->generateCommentLine($this->migrationFieldFile($module)) . "\n";
            $ctpLines .= implode("\n", $ctpLinesArray);
            $ctpLines .= "\n\n";
        }

        return $ctpLines;
    }

    /**
     * Generate lines for menu labels.
     * @param string $module Module name
     * @return string
     */
    private function generateModuleMenuLabels(string $module): string
    {
        $ctpLines = '';

        $menuLabelConfig = (new ModuleConfig(ConfigType::MENUS(), $module));
        $labelFile = (string)$menuLabelConfig->find(false);
        $menuLabelConfig = $menuLabelConfig->parseToArray();
        if (is_array($menuLabelConfig) && 0 < count($menuLabelConfig)) {
            $ctpLines .= $this->generateCommentLine(str_replace(CONFIG, 'config' . DS, $labelFile)) . "\n";
            $menuItems = $this->translateMenuItems(array_shift($menuLabelConfig));

            $ctpLinesArray = [];
            $ctpLinesArray = array_map([$this, "generateLine"], $menuItems);
            $ctpLines .= implode("\n", $ctpLinesArray);
            $ctpLines .= "\n\n";
        }

        return $ctpLines;
    }

    /**
     * Generate lines for menu labels.
     * @param string $module Module name
     * @return string
     */
    private function generatePanelTitleLabels(string $module): string
    {
        $ctpLines = '';

        $table = TableRegistry::getTableLocator()->get($module);

        $views = ['view', 'edit', 'add'];
        foreach ($views as $view) {
            $panels = CsvUtility\Field::getCsvView($table, $view, true, true);

            if (empty($panels)) {
                continue;
            }

            $ctpLines .= $this->generateCommentLine("Module: " . $module . ", CsvView: " . $view) . "\n";
            $actionTitles = array_keys($panels);

            $ctpLinesArray = [];
            $ctpLinesArray = array_map([$this, "generateLine"], $actionTitles);
            $ctpLines .= implode("\n", $ctpLinesArray);
            $ctpLines .= "\n\n";
        }

        return $ctpLines;
    }

    /**
     * Check fields and their types.
     * @param string $module Module name
     * @return mixed[]
     */
    private function migrationFieldLabels(string $module): array
    {
        $mc = $this->getMigrationConfig($module, []);
        $fields = $mc->parseToArray();

        $fieldLabels = [];

        foreach ($fields as $field) {
            $fieldLabels[] = Inflector::humanize(Inflector::underscore($field['name']));
        }

        return $fieldLabels;
    }

    /**
     * Return migration file path
     * @param string $module Module name
     * @return string
     */
    private function migrationFieldFile(string $module): string
    {
        $mc = $this->getMigrationConfig($module, []);
        $filePath = str_replace(CONFIG, 'config' . DS, (string)$mc->find(false));

        return $filePath;
    }

    /**
     * Main configuration file
     *
     * @param string $module Module.
     * @param string[] $options Options.
     * @return ModuleConfig Module Config.
     */
    protected function getModuleConfig(string $module, array $options = []): ModuleConfig
    {
        $configFile = empty($options['configFile']) ? null : $options['configFile'];
        $mc = new ModuleConfig(ConfigType::MODULE(), $module, $configFile, ['cacheSkip' => true]);

        return $mc;
    }

    /**
     * View configuration file
     *
     * @param string $module Module.
     * @param string[] $options Options.
     * @return ModuleConfig Config.
     */
    protected function getViewConfig(string $module, array $options = []): ModuleConfig
    {
        $configFile = empty($options['configFile']) ? null : $options['configFile'];
        $mc = new ModuleConfig(ConfigType::VIEW(), $module, $configFile, ['cacheSkip' => true]);

        return $mc;
    }

    /**
     * Creates a custom instance of `ModuleConfig` with a parser, schema and
     * extra validation.
     *
     * @param string $module Module.
     * @param string[] $options Options.
     * @return ModuleConfig Module Config.
     */
    protected function getMigrationConfig(string $module, array $options = []): ModuleConfig
    {
        $configFile = empty($options['configFile']) ? null : $options['configFile'];
        $mc = new ModuleConfig(ConfigType::MIGRATION(), $module, $configFile, ['cacheSkip' => true]);
        $schema = $mc->createSchema(['lint' => true]);
        $mc->setParser(new Parser($schema, ['lint' => true, 'validate' => true]));

        return $mc;
    }

    /**
     * Method for generating translation line
     * @param  string $item Text for translation
     * @return string
     */
    private function generateLine(string $item): string
    {
        return "echo __('" . $item . "');";
    }

    /**
     * Method for generating comment line
     * @param  string $text Text to comment
     * @return string
     */
    private function generateCommentLine(string $text): string
    {
        return '/* ' . $text . ' */';
    }

    /**
     * Method for generating translation lines fro menus
     * @param  mixed[] $menu Text for translation
     * @return mixed[]
     */
    private function translateMenuItems(array $menu): array
    {
        $menuItems = [];

        foreach ($menu as $key => $menuItem) {
            if (isset($menuItem['label'])) {
                $menuItems[] = $menuItem['label'];
            }
            if (isset($menuItem['desc'])) {
                $menuItems[] = $menuItem['desc'];
            }

            if (isset($menuItem['children']) && is_array($menuItem['children']) && 0 < count($menuItem['children'])) {
                $childrenItems = $this->translateMenuItems($menuItem['children']);
                $menuItems = array_merge($menuItems, $childrenItems);
            }
        }

        return $menuItems;
    }

    /**
     * @param string $module Module Name
     * @return void|null|int
     */
    public function setModule(string $module = '')
    {
        if (isset($this->params['module'])) {
            $this->module = $this->params['module'];
        } else {
            $this->module = $module;
        }
    }

    /**
     * @return string
     */
    private function getModule(): string
    {
        return $this->module;
    }
}
