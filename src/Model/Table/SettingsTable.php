<?php

namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validation;
use Cake\Validation\Validator;
use CsvMigrations\FieldHandlers\Config\ConfigFactory;
use CsvMigrations\FieldHandlers\CsvField;

/**
 * Settings Model
 *
 * @method \App\Model\Entity\Setting get($primaryKey, $options = [])
 * @method \App\Model\Entity\Setting newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Setting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Setting|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Setting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Setting[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Setting findOrCreate($search, callable $callback = null, $options = [])
 */
class SettingsTable extends Table
{

    public const SCOPE_APP = 'app';
    public const CONTEXT_APP = 'app';
    public const SCOPE_USER = 'user';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('settings');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('key')
            ->maxLength('key', 255)
            ->requirePresence('key', 'create')
            ->notEmpty('key')
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('value')
            ->maxLength('value', 255)
            ->requirePresence('value', 'create')
            ->allowEmpty('value');

        $validator
            ->scalar('scope')
            ->maxLength('scope', 10)
            ->requirePresence('scope', 'create')
            ->notEmpty('scope');

        $validator
            ->maxLength('context', 255)
            ->requirePresence('context', 'create')
            ->notEmpty('scope');

        $validator->add('value', 'custom', [
            'rule' => [$this, 'valueValidator'],
        ]);

        $validator->add('context', 'custom', [
            'rule' => [$this, 'contextValidator'],
        ]);

        return $validator;
    }

    /**
     * Validate the context according the scope value
     * @param string $value Value of the $context
     * @param mixed[] $context The entity
     * @return bool True if validate
     */
    public function contextValidator(string $value, array $context): bool
    {
        $scope = $context['data']['scope'];

        switch ($scope) {
            case self::SCOPE_APP:
                return $value === self::CONTEXT_APP ? true : false;

            case self::SCOPE_USER:
                return Validation::uuid($value);

            default:
                return false;
        }
    }

    /**
     * Validate the field from the type in settings.php
     * @param string $value Value of the field
     * @param mixed[] $context The entity
     * @return bool True if validate
     */
    public function valueValidator(string $value, array $context): bool
    {
        $type = $context['data']['type'];
        $type !== 'list' ?: $type = 'string';

        $config = ConfigFactory::getByType($type, 'value');
        $validationClass = $config->getProvider('validationRules');
        $validationRules = new $validationClass($config);

        $validator = $validationRules->provide(new Validator(), [
            'fieldDefinitions' => new CsvField(['name' => 'value'])
        ]);

        return empty($validator->errors(['value' => $value]));
    }

    /**
     * if the key exist in the DB, will create and validate an entity.
     * @param  string $key   key (alias) of the DB
     * @param  string $value value
     * @param  string $type  type
     * @param  string $scope app, user, os?, env?
     * @param  string $context uuid, value
     * @return \App\Model\Entity\Setting|void
     */
    public function createEntity(string $key, string $value, string $type, string $scope, string $context)
    {
        // It will check if there is any record with a key = $key.
        // if doesn't, it means that Settings table is not updated with settings.php.
        $this->find('all')->where(['key' => $key])->firstOrFail();
        // select based on key, scope, context
        $entity = $this->find('all')->where(['key' => $key, 'scope' => $scope, 'context' => $context])->first();
        // Mainly need for phpstan
        if (is_array($entity)) {
            return;
        }

        // It will storage only the modified settings
        if (!is_null($entity) && $entity['value'] === $value) {
            return;
        }

        // If the user setting match the app setting, the entity will be deleted or not saved
        $dataApp = $this->find('all')->where(['key' => $key, 'scope' => self::SCOPE_APP, 'context' => self::CONTEXT_APP])->first();
        if ($scope === self::SCOPE_USER && $value === $dataApp['value']) {
            !is_null($entity) ? $this->delete($entity) : '';

            return;
        }
        $type !== 'list' ?: $type = 'string';

        $params = [
            'key' => $key,
            'value' => $value,
            'scope' => $scope,
            'context' => $context,
            // dynamic field to pass type to the validator
            'type' => $type
        ];

        // Check if the user has already a record with the key. If true will update instead of create a new one
        $newEntity = is_null($entity) ? $this->newEntity($params) : $this->patchEntity($entity, $params);

        return $newEntity;
    }

    /**
     * Get all the Setting configuration and filter it base on the user
     * scope describe in settings.php
     *
     * @param mixed[] $dataSettings Data to filter
     * @param mixed[] $userScope list of scope of the user
     * @return mixed[] Settings own by the user
     * @throws \RuntimeException when settings.php structure is broke
     */
    public function filterSettings(array $dataSettings, array $userScope): array
    {
        $filter = array_filter(Hash::flatten($dataSettings), function ($value) use ($userScope) {
                return in_array($value, $userScope);
        });
        $dataFlatten = [];

        foreach ($filter as $key => $value) {
            $p = explode('.', $key);
            // ex: 'Config.UI.Theme.Title.scope.0'
            // the structure must be 4 default layer plus two
            if (count($p) < 6) {
                throw new \RuntimeException("broken configuration in Settings");
            }
            $p = $p[0] . '.' . $p[1] . '.' . $p[2] . '.' . $p[3];
            $dataFlatten[$p] = Hash::extract($dataSettings, $p);
        }
        // $dataFiltered has now only fields belonging to the user scope
        $dataFiltered = Hash::expand($dataFlatten);

        return $dataFiltered;
    }

    /**
     * getAliasDiff() return the missing alias in the DB
     *
     * @param mixed[] $settings Array with settings
     * @return mixed[]
     */
    public function getAliasDiff(array $settings = []): array
    {
        // Array with all the alias from the config
        $alias = [];
        foreach ($settings as $data) {
            // check is the alias exist in the Configure
            Configure::readOrFail($data);
            $alias[] = $data;
        }

        // Array with all the alias from the db
        $fromDB = $this->find()->extract('key')->toArray();
        $diff = array_values(array_diff($alias, $fromDB));

        return $diff;
    }

    /**
     * Custom finder
     *
     * @param Query $query Default query
     * @param mixed[] $options where options
     * @return mixed[]
     */
    public function findDataApp(Query $query, array $options): array
    {
        return $this->find('list', ['keyField' => 'key', 'valueField' => 'value'])
              ->where($options)
              ->toArray();
    }

    /**
     * Reset the Cache after a value is changed
     * @return void
     */
    public function afterSave(): void
    {
        Cache::delete('Settings');
    }
}
