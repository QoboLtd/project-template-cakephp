<?php

namespace App\Event\Plugin\Menu\View;

use App\Access\CapabilityTrait;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Menu\Event\EventName as MenuEventName;
use Menu\MenuBuilder\MenuInterface;
use Menu\MenuBuilder\MenuItemContainerInterface;
use Menu\MenuBuilder\MenuItemInterface;

class AccessMenuListener implements EventListenerInterface
{
    use CapabilityTrait;

    /**
     * @inheritdoc
     *
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            (string)MenuEventName::GET_MENU_ITEMS() => [
                'callable' => 'getMenuItems',
                'priority' => 100
            ]
        ];
    }

    /**
     * Disables all menu items that the provided user doesn't have access to
     *
     * @param Event $event Event object
     * @param string $name Menu name
     * @param array $user Current user
     * @param bool $fullBaseUrl Flag for fullbase url on menu links
     * @param array $modules Modules to fetch menu items for
     * @param MenuInterface|null $menu Menu object to be updated
     * @return void
     */
    public function getMenuItems(Event $event, $name, array $user, $fullBaseUrl = false, array $modules = [], MenuInterface $menu = null)
    {
        if ($menu === null) {
            return;
        }

        $this->disableUnauthorisedItems($menu, $user);
        $event->setResult($menu);
    }

    /**
     * Lazy checking if the specified user has access to the specified menu items.
     * If not, the menu item will be marked as disabled.
     * Recursively, applies the checks to all levels starting from the specified container.
     *
     * @param MenuItemContainerInterface $container Starting menu container
     * @param array $user Current user
     * @return void
     */
    private function disableUnauthorisedItems(MenuItemContainerInterface $container, array $user)
    {
        /** @var MenuItemInterface $item */
        foreach ($container->getMenuItems() as $item) {
            $item->disableIf(function (MenuItemInterface $item) use ($user) {
                return !$this->_checkAccess($item->getUrl(), $user);
            });

            $this->disableUnauthorisedItems($item, $user);
        }
    }
}