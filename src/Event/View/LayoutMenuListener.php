<?php
namespace App\Event\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Search\Controller\Traits\SearchableTrait;

class LayoutMenuListener implements EventListenerInterface
{
    use SearchableTrait;

    /**
     * Menu element name
     */
    const MENU_ELEMENT = 'Menu.menu';

    const SEARCH_FORM_ELEMENT = 'Search.search_form';

    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'QoboAdminPanel.Element.Navbar.Menu.Top' => 'getSearchForm'
        ];
    }

    /**
     * Method that adds elements to index View top menu.
     *
     * @param  Cake\Event\Event     $event   Event object
     * @return Cake\Event\Event
     */
    public function getSearchForm(Event $event)
    {
        if (!$event->subject()->elementExists(static::SEARCH_FORM_ELEMENT)) {
            return;
        }

        $tableName = $event->subject()->request->controller;
        if ($event->subject()->request->plugin) {
            $tableName = $event->subject()->request->plugin . '.' . $tableName;
        }
        // skip non-searchable models
        if (!$this->_isSearchable($tableName)) {
            return;
        }

        $searchFormUrl = [
            'plugin' => $event->subject()->request->plugin,
            'controller' => $event->subject()->request->controller,
            'action' => 'search'
        ];

        $menu[] = [
            'label' => $event->subject()->element(static::SEARCH_FORM_ELEMENT),
            'url' => $searchFormUrl,
            'capabilities' => 'fromUrl'
        ];

        if ($event->subject()->elementExists(static::MENU_ELEMENT)) {
            $html = $event->subject()->element(static::MENU_ELEMENT, ['menu' => $menu, 'renderAs' => 'provided']);
        } else {
            $html = $event->subject()->element(static::SEARCH_FORM_ELEMENT);
        }

        $event->result = $html . $event->result;

        return $event->result;
    }
}
