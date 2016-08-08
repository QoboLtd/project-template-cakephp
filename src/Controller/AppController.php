<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use AuditStash\Meta\RequestMetadata;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use RolesCapabilities\Capability;
use RolesCapabilities\CapabilityTrait;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    use CapabilityTrait;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        // Temporarily disabled. @todo Find out why this is returning 403 on API calls.
        // $this->loadComponent('Csrf');
        $this->loadComponent('CakeDC/Users.UsersAuth');
        $this->Auth->config('authorize', false);
        $this->Auth->config('loginRedirect', '/');
        $this->loadComponent('RolesCapabilities.Capability');
        $this->loadComponent('CsvMigrations.CsvView');
        $this->loadComponent('Search.Searchable');
    }

    /**
     * Callack method.
     *
     * @param  Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        /*
        if user not logged in, redirect him to login page
         */
        try {
            $this->_checkAccess($event);
        } catch (ForbiddenException $e) {
            if (empty($this->Auth->user())) {
                $this->redirect('/login');
            } else {
                throw new ForbiddenException($e->getMessage());
            }
        }

        $this->_setIframeRendering();

        EventManager::instance()->on(new RequestMetadata($this->request, $this->Auth->user('id')));
    }

    /**
     * Allow/Prevent page rendering in iframe.
     *
     * @return void
     */
    protected function _setIframeRendering()
    {
        $renderIframe = trim((string)getenv('ALLOW_IFRAME_RENDERING'));

        if ('' !== $renderIframe) {
            $this->response->header('X-Frame-Options', $renderIframe);
        }
    }

    /**
     * Get list of controller's skipped actions.
     *
     * @param  string $controllerName Controller name
     * @return array
     */
    public static function getSkipActions($controllerName)
    {
        $result = [
            'getMenu',
            'getCapabilities',
            'getSkipControllers',
            'getSkipActions'
        ];
        switch ($controllerName) {
            case 'CakeDC\Users\Controller\UsersController':
                $result = array_merge($result, [
                    'failedSocialLogin',
                    'failedSocialLoginListener',
                    'getUsersTable',
                    'requestResetPassword',
                    'resendTokenValidation',
                    'resetPassword',
                    'setUsersTable',
                    'socialEmail',
                    'socialLogin',
                    'twitterLogin',
                    'validate',
                    'validateEmail',
                    'validateReCaptcha',
                    'logout',
                    'login'
                ]);
                break;
        }

        return $result;
    }
}
