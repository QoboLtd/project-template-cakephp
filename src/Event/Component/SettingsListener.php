<?php

namespace App\Event\Component;

use App\Feature\Factory as FeatureFactory;
use App\Settings\DbConfig;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\I18n\I18n;
use Webmozart\Assert\Assert;

class SettingsListener implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
             'Controller.startup' => [ 'callable' => 'loadUserSettings', 'priority' => 100],
        ];
    }

    /**
     * Load User settings and overwrite defaults
     *
     * @param \Cake\Event\Event $event Event instance
     * @param string|null $userId User ID
     * @return void
     */
    public function loadUserSettings(Event $event, ?string $userId = null): void
    {
        $feature = FeatureFactory::get('Module' . DS . 'Settings');
        if (!$feature->isActive()) {
            return;
        }

        if (!empty($userId)) {
            $this->loadUserDbConfig($userId);

            return;
        }

        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, Controller::class);
        $userId = $this->getUserFromController($subject);
        if (empty($userId)) {
            return;
        }

        $this->loadUserDbConfig($userId);

        $locale = Configure::read('App.defaultLocale');
        I18n::setLocale($locale);
    }

    /**
     * Retrieves and returns the User ID from the provided Controller instance
     *
     * @param \Cake\Controller\Controller $controller Controller instance
     * @return string|null
     */
    private function getUserFromController(Controller $controller): ?string
    {
        if ($controller->components()->has('Auth')) {
            return $controller->Auth->user('id');
        }

        return null;
    }

    /**
     * Loads the configuration for the specified User ID
     *
     * @param string $userId User ID
     * @return void
     */
    private function loadUserDbConfig(string $userId): void
    {
        Configure::config('dbconfig', new DbConfig('user', $userId));
        Configure::load('Settings', 'dbconfig', true);
    }
}
