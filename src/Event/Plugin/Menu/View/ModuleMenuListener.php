<?php

namespace App\Event\Plugin\Menu\View;

use App\Feature\Factory as FeatureFactory;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Utility\Hash;
use Menu\Event\EventName as MenuEventName;
use Menu\MenuBuilder\MenuFactory;
use Menu\MenuBuilder\MenuInterface;
use Qobo\Utils\Module\ModuleRegistry;
use Qobo\Utils\Utility;

class ModuleMenuListener implements EventListenerInterface
{
    /**
     * Menu item defaults.
     *
     * @var array
     */
    protected $defaults = [
        'url' => '#',
        'label' => 'Undefined',
        'order' => 0,
        'target' => '_self',
        'children' => [],
        'desc' => '',
    ];

    /**
     * @inheritdoc
     *
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            (string)MenuEventName::GET_MENU_ITEMS() => 'getMenuItems',
        ];
    }

    /**
     * Method that updates the provided Menu to include links defined menus.json files
     *
     * @param Event $event Event object
     * @param string $name Menu name
     * @param mixed[] $user Current user
     * @param bool $fullBaseUrl Flag for fullbase url on menu links
     * @param mixed[] $modules Modules to fetch menu items for
     * @param MenuInterface|null $menu Menu object to be updated
     * @return void
     * @throws \Exception
     */
    public function getMenuItems(
        Event $event,
        string $name,
        array $user,
        bool $fullBaseUrl = false,
        array $modules = [],
        MenuInterface $menu = null
    ): void {
        if (empty($modules)) {
            $modules = Utility::findDirs(Configure::readOrFail('CsvMigrations.modules.path'));
            /**
             * @var \Menu\MenuBuilder\MenuInterface $menu
             */
            $menu = $menu;
            MenuFactory::addToMenu($menu, $this->getModulesMenuItems($modules, $name));
            $event->setResult($menu);
        } else {
            $menuItems = $this->getModulesMenuItems($modules, $name);
            $event->setResult($menuItems);
        }
    }

    /**
     * Menu links getter.
     *
     * @param mixed[] $modules Modules list
     * @param string $menuName Menu name
     * @return mixed[]
     * @throws \Exception
     */
    protected function getModulesMenuItems(array $modules, string $menuName): array
    {
        if (empty($modules)) {
            return [];
        }

        $result = [];
        foreach ($modules as $module) {
            $feature = FeatureFactory::get('Module' . DS . $module);
            if (!$feature->isActive()) {
                continue;
            }

            $links = $this->getModuleMenuItems($module, $menuName);
            $result = array_merge($result, $links);
        }

        return $result;
    }

    /**
     * Module links getter.
     *
     * @param string $module Module name
     * @param string $menuName Menu name
     * @return mixed[]
     * @throws \Exception
     */
    protected function getModuleMenuItems(string $module, string $menuName): array
    {
        return $this->applyModuleDefaults(
            $module,
            ModuleRegistry::getModule($module)->getMenu($menuName)
        );
    }

    /**
     * Applies the module defaults on the provided menu item
     *
     * @param string $module Module's name
     * @param mixed[] $items List of menu items
     * @return mixed[] The provided list of menu items including the defaults
     * @throws \Exception
     */
    private function applyModuleDefaults(string $module, array $items): array
    {
        return MenuFactory::applyDefaults($items, $this->getModuleDefaults($module));
    }

    /**
     * Returns the default values for the specified module.
     *
     * @param string $module Module's name
     * @return mixed[] The defaults
     * @throws \Exception
     */
    private function getModuleDefaults(string $module): array
    {
        return [
            'icon' => Hash::get(ModuleRegistry::getModule($module)->getConfig(), 'table.icon'),
        ];
    }
}
