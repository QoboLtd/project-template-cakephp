
`(new ModuleConfig(ConfigType::MODULE(), $this->moduleName, null, $this->moduleConfigOptions))->parseToArray();`


$migration = (new ModuleConfig(ConfigType::MIGRATION(), $this->moduleName, null, $this->moduleConfigOptions))->parseToArray();
$lists = $this->getModuleLists();
$fields = (new ModuleConfig(ConfigType::FIELDS(), $this->moduleName, null, $this->moduleConfigOptions))->parseToArray();
$menus = (new ModuleConfig(ConfigType::MENUS(), $this->moduleName, null, $this->moduleConfigOptions))->parseToArray();
$views = $this->getModuleViews();
