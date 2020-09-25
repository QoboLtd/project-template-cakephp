<?php

namespace App\Shell;

use CakeDC\Users\Model\Behavior\SocialBehavior;
use CakeDC\Users\Shell\UsersShell as BaseShell;
use Cake\Datasource\EntityInterface;
use Webmozart\Assert\Assert;

class UsersShell extends BaseShell
{
    /**
     * @var \App\Model\Table\UsersTable $Users
     */
    public $Users;

    /**
     * Add a new superadmin user
     *
     * @return void
     */
    public function addSuperuser()
    {
        $username = $this->getUsername();
        $password = $this->getPassword();
        $email = $this->getEmail($username);

        $user = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'active' => 1,
        ];

        $userEntity = $this->Users->newEntity($user);
        $userEntity->set('is_superuser', true);
        $userEntity->set('role', 'superuser');
        $savedUser = $this->Users->save($userEntity);
        if (!empty($savedUser)) {
            $this->printUserInfo($savedUser, $password);
        } else {
            $this->printUserErrors($userEntity);
            $this->abort((string)__d('CakeDC/Users', 'Failed to add superuser'));
        }
    }

    /**
     * Return a username for the new user
     *
     * If the username is provided as an argument,
     * return that.  Otherwise, generate a unique
     * username for the super user.
     *
     * @return string
     */
    protected function getUsername(): string
    {
        if (!empty($this->params['username'])) {
            return $this->params['username'];
        }

        $behavior = $this->Users->getBehavior('Social');
        Assert::isInstanceOf($behavior, SocialBehavior::class);

        return $behavior->generateUniqueUsername('superadmin');
    }

    /**
     * Return a password for the new user
     *
     * If the password is provided as an argument,
     * return that.  Otherwise, generate a random
     * password.
     *
     * @return string
     */
    protected function getPassword(): string
    {
        if (!empty($this->params['password'])) {
            return $this->params['password'];
        }

        return $this->_generateRandomPassword();
    }

    /**
     * Return an email for the new user
     *
     * If the email is provided as an argument,
     * return that.  Otherwise, generate an email
     * based on a given username.
     *
     * @param string $username Username
     * @return string
     */
    protected function getEmail(string $username): string
    {
        if (!empty($this->params['email'])) {
            return $this->params['email'];
        }

        return $username . '@example.com';
    }

    /**
     * Print out user information
     *
     * @param \Cake\Datasource\EntityInterface $user User entity
     * @param string $password Plain text password
     * @return void
     */
    protected function printUserInfo(EntityInterface $user, string $password): void
    {
        $this->out('<success>' . __d('CakeDC/Users', 'Superuser added successfully.') . '</success>');
        $this->out('<info>' . __d('CakeDC/Users', 'User Id : {0}', $user->get('id')) . '</info>');
        $this->out('<info>' . __d('CakeDC/Users', 'Username: {0}', $user->get('username')) . '</info>');
        $this->out('<info>' . __d('CakeDC/Users', 'Email   : {0}', $user->get('email')) . '</info>');
        $this->out('<info>' . __d('CakeDC/Users', 'Password: {0}', $password) . '</info>');
    }

    /**
     * Print out user errors
     *
     * @param \Cake\Datasource\EntityInterface $user User entity
     * @return void
     */
    protected function printUserErrors(EntityInterface $user): void
    {
        $this->err(__d('CakeDC/Users', 'Errors while trying to add a superuser:'));

        /**
         * @var array $errors
         */
        $errors = $user->getErrors();

        collection($errors)->each(function ($error, $field) {
            $this->err(__d('CakeDC/Users', 'Field "{0}" error: {1}', $field, implode(',', $error)));
        });
    }
}
