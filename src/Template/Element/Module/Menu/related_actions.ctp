<?php
use App\Menu\MenuName;
use Cake\Event\Event;
use Menu\Event\EventName;
use Menu\MenuBuilder\Menu;
use Menu\MenuBuilder\MenuInterface;
use Menu\MenuBuilder\MenuItemFactory;

$menu = new Menu();

$menu->addMenuItem(MenuItemFactory::createMenuItem([
    'url' => ['prefix' => false, 'plugin' => $plugin, 'controller' => $controller, 'action' => 'view', $entity->id],
    'icon' => 'eye',
    'label' => __d('Qobo/ProjectTemplateCakephp', 'View'),
    'type' => 'link_button',
    'order' => 10,
]));

$menu->addMenuItem(MenuItemFactory::createMenuItem([
    'url' => ['prefix' => false, 'plugin' => $plugin, 'controller' => $controller, 'action' => 'edit', $entity->id],
    'icon' => 'pencil',
    'label' => __d('Qobo/ProjectTemplateCakephp', 'Edit'),
    'type' => 'link_button',
    'order' => 20,
]));

$menu->addMenuItem(MenuItemFactory::createMenuItem([
    'url' => [
        'prefix' => 'api',
        'plugin' => $plugin,
        'controller' => $controller,
        'action' => 'delete',
        '_ext' => 'json',
        $entity->id
    ],
    'icon' => 'trash',
    'label' => __d('Qobo/ProjectTemplateCakephp', 'Delete'),
    'dataType' => 'ajax-delete-record',
    'type' => 'link_button',
    'confirmMsg' => __(
        'Are you sure you want to delete {0}?',
        $entity->has($displayField) && !empty($entity->{$displayField}) ?
            strip_tags($entity->{$displayField}) :
            'this record'
    ),
    'order' => 30
]));

$menu->addMenuItem(MenuItemFactory::createMenuItem([
    'url' => [
        'prefix' => false,
        'plugin' => $plugin,
        'controller' => $associationController,
        'action' => 'unlink',
        $associationId,
        $associationName,
        $entity->id,
    ],
    'icon' => 'chain-broken',
    'label' => __d('Qobo/ProjectTemplateCakephp', 'Unlink'),
    'dataType' => 'ajax-delete-record',
    'type' => 'postlink_button',
    'confirmMsg' => __(
        'Are you sure you want to unlink {0}?',
        $entity->has($displayField) && !empty($entity->{$displayField}) ?
            strip_tags($entity->{$displayField}) :
            'this record'
    ),
    'order' => 40
]));

// Dispatch the event so that it can be extended
$event = new Event((string)EventName::GET_MENU_ITEMS(), $entity, [
    'name' => MenuName::MODULE_RELATED_ROW,
    'user' => $user,
    'fullBaseUrl' => false,
    'modules' => [],
    'menu' => $menu,
    'entity' => $entity,
]);
$this->getEventManager()->dispatch($event);

$menu = $event->getResult();
if (!($menu instanceof MenuInterface)) {
    return;
}

echo $this->element('menu-render', ['menu' => $menu, 'user' => $user, 'menuType' => 'actions']);
