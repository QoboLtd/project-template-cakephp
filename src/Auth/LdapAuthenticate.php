<?php

namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

class LdapAuthenticate extends BaseAuthenticate
{
    use LogTrait;

    /**
     * Default LDAP protocol version.
     */
    public const DEFAULT_VERSION = 3;

    /**
     * Default LDAP port.
     */
    public const DEFAULT_PORT = 389;

    /**
     * LDAP Object.
     *
     * @var resource|null
     */
    protected $_connection = null;

    /**
     * {@inheritDoc}
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

        // set LDAP configuration
        $this->config(Configure::read('Ldap'));

        if (empty($this->_config['host'])) {
            throw new InternalErrorException('LDAP Server not specified.');
        }

        if (empty($this->_config['version'])) {
            $this->_config['version'] = static::DEFAULT_VERSION;
        }

        if (empty($this->_config['port'])) {
            $this->_config['port'] = static::DEFAULT_PORT;
        }

        $this->_connect();
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        $user = $this->getUser($request);

        if (empty($user)) {
            return false;
        }

        return $this->_saveUser($user, $request);
    }

    /**
     * LDAP connect
     * @throws \Exception if cannot connect to the LDAP server
     * @return void
     */
    protected function _connect(): void
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        $connection = @ldap_connect($this->_config['host'], $this->_config['port']);

        if (false === $connection) {
            $this->log('Unable to connect to specified LDAP Server.', LogLevel::CRITICAL);

            return;
        }

        // set LDAP options
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, (int)$this->_config['version']);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 5);

        $this->_connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(ServerRequest $request)
    {
        if (null === $this->_connection) {
            return false;
        }

        if (! ($request->getData('username')) || ! ($request->getData('password'))) {
            return false;
        }

        /**
         * @var string $username
         */
        $username = $request->getData('username');
        /**
         * @var string $password
         */
        $password = $request->getData('password');

        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        $bind = @ldap_bind($this->_connection, $username, $password);
        if (false === $bind) {
            $this->log(sprintf('LDAP server bind failed for user "%s".', $username), LogLevel::CRITICAL);

            return false;
        }

        $filter = '(' . $this->_config['filter'] . '=' . $username . ')';
        $attributes = $this->_config['attributes']();
        $search = ldap_search($this->_connection, $this->_config['baseDn'], $filter, array_keys($attributes));
        if (false === $search) {
            $this->log('Failed to retrieve search result.', LogLevel::CRITICAL);

            return false;
        }

        $entry = ldap_first_entry($this->_connection, $search);
        if (false === $entry) {
            $this->log('Failed to retrieve result entry.', LogLevel::CRITICAL);

            return false;
        }

        return ldap_get_attributes($this->_connection, $entry);
    }

    /**
     * Save LDAP user to the Database.
     *
     * @param mixed[] $data LDAP user info.
     * @param \Cake\Http\ServerRequest $request Request object.
     * @return mixed[]|bool User info or false if failed.
     */
    protected function _saveUser(array $data, ServerRequest $request)
    {
        // return false if user data empty or username field is not set
        if (empty($data)) {
            return false;
        }

        $data = $this->_mapData($data);

        $table = TableRegistry::get($this->_config['userModel']);

        // look for the user in the database
        $query = $table->find('all', [
            'conditions' => [$this->_config['fields']['username'] => $request->getData('username')]
        ])->enableHydration(true);

        $entity = $query->first();

        // user already exists, just return the existing entity
        if ($entity instanceof EntityInterface) {
            return $entity->toArray();
        }

        // set username
        $data[$this->_config['fields']['username']] = $request->getData('username');

        // use random password for local entity of ldap user
        $data[$this->_config['fields']['password']] = uniqid();

        // activate user by default
        $data['active'] = true;

        // save new user entity
        $entity = $table->newEntity();
        $entity = $table->patchEntity($entity, $data);

        if ($table->save($entity)) {
            return $entity->toArray();
        } else {
            return false;
        }
    }

    /**
     * Map LDAP fields to database fields.
     *
     * @param  mixed[] $data LDAP user info.
     * @return mixed[]
     */
    protected function _mapData(array $data = []): array
    {
        $result = [];
        if (empty($data)) {
            return $result;
        }

        $attributes = $this->_config['attributes']();

        foreach ($attributes as $k => $v) {
            // skip non-mapped fields
            if (empty($v)) {
                continue;
            }

            $result[$v] = Hash::get($data, $k . '.0');
        }

        return $result;
    }

    /**
     * Destructor method.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->_disconnect();
    }

    /**
     * Disconnect LDAP connection.
     *
     * @return void
     */
    protected function _disconnect(): void
    {
        if (null === $this->_connection) {
            return;
        }

        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        @ldap_unbind($this->_connection);
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        @ldap_close($this->_connection);
    }
}
