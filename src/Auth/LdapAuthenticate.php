<?php
namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Log\LogTrait;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use ErrorException;
use Psr\Log\LogLevel;

class LdapAuthenticate extends BaseAuthenticate
{
    use LogTrait;

    /**
     * Default LDAP protocol version.
     */
    const DEFAULT_VERSION = 3;

    /**
     * Default LDAP port.
     */
    const DEFAULT_PORT = 389;

    /**
     * LDAP Object.
     *
     * @var object
     */
    protected $_connection;

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
    public function authenticate(Request $request, Response $response)
    {
        $user = $this->getUser($request);

        if (empty($user)) {
            return false;
        }

        return $this->_saveUser($user, $request);
    }

    /**
     * LDAP connect
     *
     * @return void
     */
    protected function _connect()
    {
        try {
            $this->_connection = @ldap_connect($this->_config['host'], $this->_config['port']);
            // set LDAP options
            ldap_set_option($this->_connection, LDAP_OPT_PROTOCOL_VERSION, (int)$this->_config['version']);
            ldap_set_option($this->_connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($this->_connection, LDAP_OPT_NETWORK_TIMEOUT, 5);
        } catch (\Exception $e) {
            $this->log('Unable to connect to specified LDAP Server.', LogLevel::CRITICAL);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getUser(Request $request)
    {
        if (!isset($request->data['username']) || !isset($request->data['password'])) {
            return false;
        }

        try {
            $bind = @ldap_bind($this->_connection, $request->data['username'], $request->data['password']);
            if ($bind) {
                $filter = '(' . $this->_config['filter'] . '=' . $request->data['username'] . ')';
                $attributes = $this->_config['attributes']();
                $search = ldap_search($this->_connection, $this->_config['baseDn'], $filter, array_keys($attributes));
                $entry = ldap_first_entry($this->_connection, $search);

                return ldap_get_attributes($this->_connection, $entry);
            } else {
                $this->log('LDAP server bind failed for [' . $request->data['username'] . '].', LogLevel::CRITICAL);
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }

        return false;
    }

    /**
     * Save LDAP user to the Database.
     *
     * @param  array $data LDAP user info.
     * @param \Cake\Network\Request $request Request object.
     * @return array|bool User info or false if failed.
     */
    protected function _saveUser(array $data, Request $request)
    {
        // return false if user data empty or username field is not set
        if (empty($data)) {
            return false;
        }

        $data = $this->_mapData($data);

        $table = TableRegistry::get($this->_config['userModel']);

        // look for the user in the database
        $query = $table->find('all', [
            'conditions' => [$this->_config['fields']['username'] => $request->data['username']]
        ]);
        $entity = $query->first();

        // user already exists, just return the existing entity
        if ($entity) {
            return $entity->toArray();
        }

        // set username
        $data[$this->_config['fields']['username']] = $request->data['username'];

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
     * @param  array $data LDAP user info.
     * @return array
     */
    protected function _mapData(array $data = [])
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
    protected function _disconnect()
    {
        @ldap_unbind($this->_connection);
        @ldap_close($this->_connection);
    }
}
