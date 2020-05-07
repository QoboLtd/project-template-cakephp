<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * Things Model
 *
 */
class ThingsTable extends AppTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('things');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator = parent::validationDefault($validator);

        $validator->lengthBetween('phone', ['minLength' => 2, 'maxLength' => 3], __('Value must be between 2 and 3 characters long'));

        return $validator;
    }
}
