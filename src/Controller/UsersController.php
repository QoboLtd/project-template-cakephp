<?php

namespace App\Controller;

use App\Avatar\Service as AvatarService;
use App\Model\Table\UsersTable;
use CakeDC\Users\Controller\Traits\LoginTrait;
use CakeDC\Users\Controller\Traits\ProfileTrait;
use CakeDC\Users\Controller\Traits\RegisterTrait;
use CakeDC\Users\Controller\Traits\SimpleCrudTrait;
use CakeDC\Users\Controller\Traits\U2fTrait;
use CakeDC\Users\Exception\UserNotFoundException;
use CakeDC\Users\Exception\WrongPasswordException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Webmozart\Assert\Assert;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    // Use any of traits provided by CakeDC to extend the provided functionality
    use LoginTrait;
    use ProfileTrait;
    use RegisterTrait;
    use SimpleCrudTrait {
        SimpleCrudTrait::delete as defaultDelete;
    }
    use U2fTrait;

    /**
     * changeUserPassword method
     *
     * change user passwords by the superusers
     *
     * @param mixed $id user id
     * @return \Cake\Http\Response|void|null Redirects on successful edit, renders view otherwise.
     */
    public function changeUserPassword($id)
    {
        $table = $this->getUsersTable();
        Assert::isInstanceOf($table, UsersTable::class);

        $user = $table->newEntity();
        $user->id = $id;
        $redirect = ['controller' => 'Users', 'action' => 'index'];

        if ($this->request->is('post')) {
            try {
                $validator = $table->validationPasswordConfirm(new Validator());
                $user = $table->patchEntity($user, (array)$this->request->getData(), ['validate' => $validator]);

                if ($user->getErrors()) {
                    $this->Flash->error((string)__d('CakeDC/Users', 'Password could not be changed'));
                } else {
                    $user = $table->changePassword($user);
                    if ($user) {
                        $this->Flash->success((string)__d('CakeDC/Users', 'Password has been changed successfully'));

                        return $this->redirect($redirect);
                    } else {
                        $this->Flash->error((string)__d('CakeDC/Users', 'Password could not be changed'));
                    }
                }
            } catch (UserNotFoundException $exception) {
                $this->Flash->error((string)__d('CakeDC/Users', 'User was not found'));
            } catch (WrongPasswordException $wpe) {
                $this->Flash->error((string)__d('CakeDC/Users', '{0}', $wpe->getMessage()));
            }
        }

        $this->set(compact('user'));
    }

    /**
     * Upload user image
     *
     * Converts and stores user image in base64 scheme.
     *
     * @param string $id User id
     * @return \Cake\Http\Response|void|null
     */
    public function uploadImage(string $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);

        $user = $this->Users->get($id);

        $data = $this->request->getData('Users.image');

        if (! $data) {
            $this->Flash->error((string)__d('Qobo/ProjectTemplateCakephp', 'Failed to upload image, please try again.'));

            return $this->redirect($this->request->referer());
        }

        $avatarService = new AvatarService();
        /**
         * @var array $data
         */
        $data = $data;
        if (! $avatarService->isAllowedSize($data)) {
            $this->Flash->error((string)__d('Qobo/ProjectTemplateCakephp', 'Image is too large. Max size 512kb.'));

            return $this->redirect($this->request->referer());
        }

        if (! $avatarService->isImage($data)) {
            $this->Flash->error((string)__d('Qobo/ProjectTemplateCakephp', 'Unsupported image type.'));

            return $this->redirect($this->request->referer());
        }

        $processed = false;
        $resource = $avatarService->getImageResource($data);

        if (false !== $resource) {
            $processed = $this->Users->saveCustomAvatar($user, $resource);
        }

        if ($processed) {
            $this->Flash->success((string)__d('Qobo/ProjectTemplateCakephp', 'The image has been uploaded.'));
        } else {
            $this->Flash->error((string)__d('Qobo/ProjectTemplateCakephp', 'Couldn\'t upload the image'));
        }

        return $this->redirect($this->request->referer());
    }

    /**
     * editProfile method
     *
     *  Overide CakeDC edit suer record method to give ability
     * separate user record update by admin and editing profile
     * by logged in user
     *
     * @return \Cake\Http\Response|void|null
     * @throws \CakeDC\Users\Exception\UserNotFoundException When user not found.
     * @throws \Cake\Http\Exception\UnauthorizedException When user is not authorized.
     */
    public function editProfile()
    {
        $this->autoRender = false;
        $this->request->allowMethod(['patch', 'post', 'put']);

        $userId = $this->Auth->user('id');
        if (empty($userId)) {
            throw new UnauthorizedException('You have to login to complete this action!');
        }

        $user = $this->Users->get($userId);
        if (empty($user)) {
            throw new UserNotFoundException('User not found!');
        }

        $user = $this->Users->patchEntity($user, (array)$this->request->getData());

        if ($this->Users->save($user)) {
            $this->Flash->success((string)__d('Qobo/ProjectTemplateCakephp', 'Profile successfully updated'));
            $this->Auth->setUser($user->toArray());
        } else {
            $this->Flash->error((string)__d('Qobo/ProjectTemplateCakephp', 'Failed to update profile data, please try again.'));
        }

        return $this->redirect($this->request->referer());
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $table = $this->loadModel();
        $tableAlias = $table->getAlias();
        $users = $table->find()->all();
        $this->set($tableAlias, $users);
        $this->set('lockedUsers', $this->getLockedUsers());
        $this->set('tableAlias', $tableAlias);
        $this->set('_serialize', [$tableAlias, 'tableAlias']);
    }

    /**
     * View method
     *
     * @param string $id User id.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function view(string $id)
    {
        $table = $this->loadModel();
        $tableAlias = $table->getAlias();
        $entity = $table->get($id, [
            'contain' => []
        ]);

        /**
         * @var \App\Model\Table\GroupsTable $groupsTable
         */
        $groupsTable = TableRegistry::get('Groups.Groups');
        $userGroups = $groupsTable->getUserGroupsAll($id, [
            'fields' => ['id', 'name', 'description'],
            'contain' => [],
        ]);

        $this->set($tableAlias, $entity);
        $this->set('tableAlias', $tableAlias);
        $this->set('userGroups', $userGroups);
        $this->set('subordinates', $this->Users->find('all')->where(['reports_to' => $id])->toArray());
        $this->set('_serialize', [$tableAlias, $userGroups, 'tableAlias']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void|null
     * @throws \Cake\Http\Exception\ForbiddenException When user is locked.
     */
    public function delete(?string $id)
    {
        $table = $this->loadModel();
        $entity = $table->get($id, [
            'contain' => []
        ]);

        $username = $entity->get('username');
        if (in_array($username, $this->getLockedUsers())) {
            throw new ForbiddenException();
        }

        $this->defaultDelete($id);
    }

    /**
     * Allow/Prevent page rendering in iframe. In case of embed query param exists we allow iframe
     *
     * @return void
     */
    protected function _setIframeRendering(): void
    {
        $embed = Hash::get($this->request->getQueryParams(), 'embed', '');

        if (empty($embed)) {
            parent::_setIframeRendering();
        }
    }

    /**
     * Returns an array including the usernames of the currently locked users.
     *
     * @return mixed[] List of locked users
     */
    private function getLockedUsers(): array
    {
        return [
            $this->Auth->user('username'),
            getenv('DEV_USER')
        ];
    }
}
