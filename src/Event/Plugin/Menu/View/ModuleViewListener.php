<?php

namespace App\Event\Plugin\Menu\View;

use App\Menu\MenuName;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Menu\Event\EventName as MenuEventName;
use Menu\MenuBuilder\MenuInterface;
use Menu\MenuBuilder\MenuItemFactory;
use Menu\MenuBuilder\MenuItemInterface;
use Webmozart\Assert\Assert;

class ModuleViewListener implements EventListenerInterface
{
    use MenuEntityTrait;

    /**
     * @inheritdoc
     *
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            (string)MenuEventName::GET_MENU_ITEMS() => 'getMenuItems'
        ];
    }

    /**
     * Method that returns the menu for Search View
     *
     * @param Event $event Event object
     * @param string $name Menu name
     * @param mixed[] $user Current user
     * @param bool $fullBaseUrl Flag for fullbase url on menu links
     * @param mixed[] $modules Modules to fetch menu items for
     * @param MenuInterface|null $menu Menu object to be updated
     * @return void
     */
    public function getMenuItems(Event $event, string $name, array $user, bool $fullBaseUrl = false, array $modules = [], MenuInterface $menu = null): void
    {
        Assert::isInstanceOf($menu, MenuInterface::class);

        $listens = [MenuName::MODULE_VIEW];
        if (!in_array($name, $listens)) {
            return;
        }

        // Actions are available only when viewing an entity
        $entity = $event->getSubject();
        if (!($entity instanceof EntityInterface)) {
            return;
        }

        /**
         * @var \Cake\Http\ServerRequest $request
         */
        $request = Router::getRequest();
        Assert::isInstanceOf($request, ServerRequest::class);

        $menu->addMenuItem($this->getEditMenuItem($entity, $request));
        $menu->addMenuItem($this->getDeleteMenuItem($entity, $request));

        $moreActions = MenuItemFactory::createMenuItem([
            'label' => __d('Qobo/ProjectTemplateCakephp', 'More Actions'),
            'icon' => 'plus-square-o',
            'type' => 'button_group',
            'order' => 0,
        ]);

        $moreActions->addMenuItem($this->getPermissionsMenuItem($entity, $request));
        $moreActions->addMenuItem($this->getChangelogMenuItem($entity, $request));

        $menu->addMenuItem($moreActions);
        $event->setResult($event);
    }

    /**
     * Creates and returns the menu item for the permissions action
     *
     * @param EntityInterface $entity Entity to be deleted
     * @param ServerRequest $request Current server request
     * @return \Menu\MenuBuilder\MenuItemInterface
     */
    public function getPermissionsMenuItem(EntityInterface $entity, ServerRequest $request): MenuItemInterface
    {
        $plugin = $request->getParam('plugin');
        $controller = $request->getParam('controller');
        $id = $entity->get('id');

        return MenuItemFactory::createMenuItem([
            'url' => ['plugin' => $plugin, 'controller' => $controller, 'action' => 'managePermissions'],
            'attributes' => ['class' => ' '],
            'label' => __d('Qobo/ProjectTemplateCakephp', 'Permissions'),
            'icon' => 'shield',
            'type' => 'link_button_modal',
            'modal_target' => 'permissions-modal-add',
            'order' => 50,
            'viewElement' => ['modal-permissions', ['id' => $id]]
        ]);
    }

    /**
     * Creates and returns the menu item for the changelog action
     *
     * @param EntityInterface $entity Entity to be deleted
     * @param ServerRequest $request Current server request
     * @return \Menu\MenuBuilder\MenuItemInterface
     */
    public function getChangelogMenuItem(EntityInterface $entity, ServerRequest $request): MenuItemInterface
    {
        $plugin = $request->getParam('plugin');
        $controller = $request->getParam('controller');
        $id = $entity->get('id');

        return MenuItemFactory::createMenuItem([
            'url' => ['plugin' => $plugin, 'controller' => $controller, 'action' => 'changelog', $id],
            'attributes' => ['class' => ' '],
            'label' => __d('Qobo/ProjectTemplateCakephp', 'Changelog'),
            'icon' => 'book',
            'type' => 'link_button',
            'order' => 60
        ]);
    }
}
