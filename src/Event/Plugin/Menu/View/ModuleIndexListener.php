<?php

namespace App\Event\Plugin\Menu\View;

use App\Menu\MenuName;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Menu\Event\EventName as MenuEventName;
use Menu\MenuBuilder\MenuInterface;
use Menu\MenuBuilder\MenuItemFactory;
use Menu\MenuBuilder\MenuItemInterface;

class ModuleIndexListener implements EventListenerInterface
{
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
     * Method that returns menu for Module index page
     *
     * @param Event $event Event object
     * @param string $name Menu name
     * @param mixed[] $user Current user
     * @param bool $fullBaseUrl Flag for fullbase url on menu links
     * @param mixed[] $modules Modules to fetch menu items for
     * @param \Menu\MenuBuilder\MenuInterface|null $menu Menu object to be updated
     * @return void
     */
    public function getMenuItems(Event $event, string $name, array $user, bool $fullBaseUrl = false, array $modules = [], MenuInterface $menu = null): void
    {
        if ($name !== MenuName::MODULE_INDEX_TOP) {
            return;
        }
        /**
         * @var \Cake\Http\ServerRequest $request
         */
        $request = Router::getRequest();
        /**
         * @var \Menu\MenuBuilder\MenuInterface $menu
         */
        $menu = $menu;
        $menu->addMenuItem($this->getBatchMenuItem($request));
        $menu->addMenuItem($this->getImportMenuItem($request));
        $menu->addMenuItem($this->getAddMenuItem($request));
        $menu->addMenuItem($this->getDelLogItem($request));

        $event->setResult($menu);
    }

    /**
     * Creates and returns the menu item for the batch operations
     *
     * @param \Cake\Http\ServerRequest $request Current server request
     * @return \Menu\MenuBuilder\MenuItemInterface
     */
    private function getBatchMenuItem(ServerRequest $request): MenuItemInterface
    {
        $plugin = $request->getParam('plugin');
        $controller = $request->getParam('controller');

        $batchItem = MenuItemFactory::createMenuItem([
            'type' => 'button',
            'icon' => 'bars',
            'label' => __('Batch'),
            'order' => 1,
            'attributes' => [
                'id' => 'batch-button',
                'disabled' => 'disabled',
            ],
        ]);
        $batchItem->disableIf(function () {
            return !Configure::read('Search.batch.active');
        });

        $csrfToken = empty($request->getParam('_csrfToken')) ? $request->getCookie('csrfToken') : $request->getParam('_csrfToken');

        $batchItem->addMenuItem(MenuItemFactory::createMenuItem([
            'type' => 'link',
            'icon' => 'pencil',
            'label' => __('Edit'),
            'url' => ['plugin' => $plugin, 'controller' => $controller, 'action' => 'batch', 'edit'],
            'order' => 1,
            'attributes' => [
                'data-batch' => true,
                'data-batch-url' => Router::url(['plugin' => $plugin, 'controller' => $controller, 'action' => 'batch', 'edit']),
                'data-csrf-token' => $csrfToken
            ],
        ]));

        $batchItem->addMenuItem(MenuItemFactory::createMenuItem([
            'type' => 'link',
            'icon' => 'trash',
            'label' => __('Delete'),
            'order' => 10,
            'url' => ['plugin' => $plugin, 'controller' => $controller, 'action' => 'batch', 'delete'],
            'attributes' => [
                'data-batch' => true,
                'data-batch-url' => Router::url(['plugin' => $plugin, 'controller' => $controller, 'action' => 'batch', 'delete']),
                'data-batch-confirm' => __('Are you sure you want to delete the selected records?'),
                'data-csrf-token' => $csrfToken,
            ]
        ]));

        return $batchItem;
    }

    /**
     * Creates and returns the menu item for the import action
     *
     * @param \Cake\Http\ServerRequest $request Current server request
     * @return \Menu\MenuBuilder\MenuItemInterface
     */
    private function getImportMenuItem(ServerRequest $request): MenuItemInterface
    {
        $plugin = $request->getParam('plugin');
        $controller = $request->getParam('controller');

        return MenuItemFactory::createMenuItem([
            'url' => ['plugin' => $plugin, 'controller' => $controller, 'action' => 'import'],
            'icon' => 'upload',
            'label' => __('Import'),
            'type' => 'link_button',
            'order' => 10,
        ]);
    }

    /**
     * Creates and returns the menu item for the add action
     *
     * @param \Cake\Http\ServerRequest $request Current server request
     * @return \Menu\MenuBuilder\MenuItemInterface
     */
    private function getAddMenuItem(ServerRequest $request): MenuItemInterface
    {
        $plugin = $request->getParam('plugin');
        $controller = $request->getParam('controller');

        return MenuItemFactory::createMenuItem([
            'url' => ['plugin' => $plugin, 'controller' => $controller, 'action' => 'add'],
            'icon' => 'plus',
            'label' => __('Add'),
            'type' => 'link_button',
            'order' => 20,
        ]);
    }

    /**
     * Delete logs from Scheduler job page
     *
     * @param \Cake\Http\ServerRequest $request Current server request
     * @return \Menu\MenuBuilder\MenuItemInterface
     */
    private function getDelLogItem(ServerRequest $request): MenuItemInterface
    {
        $age = Configure::read('ScheduledLog.stats.age');
        $controller = $request->getParam('controller');

        $delLog = MenuItemFactory::createMenuItem([
            'url' => ['plugin' => false, 'controller' => 'ScheduledJobLogs', 'action' => 'gc'],
            'icon' => 'trash',
            'label' => __('Delete old logs'),
            'confirmMsg' => __('Are you sure? This action will delete all the scheduled job logs older than ' . ltrim($age, '-') . '.'),
            'attributes' => ['class' => 'btn btn-danger'],
            'type' => 'postlink_button',
            'order' => 20,
        ]);

        $delLog->disableIf(function () use ($controller) {
            return !($controller == 'ScheduledJobs');
        });

        return $delLog;
    }
}
