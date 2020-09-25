<?php

namespace App\Crud\Action;

use Cake\Core\App;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\ORM\Association;
use Crud\Action\BaseAction;
use Crud\Traits\FindMethodTrait;
use Crud\Traits\SerializeTrait;
use Crud\Traits\ViewTrait;
use Crud\Traits\ViewVarTrait;
use InvalidArgumentException;

/**
 * Handles 'Related' Crud actions
 */
class RelatedAction extends BaseAction
{
    use FindMethodTrait;
    use SerializeTrait;
    use ViewTrait;
    use ViewVarTrait;

    /**
     * Default settings for 'related' actions
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'scope' => 'table',
        'findMethod' => 'all',
        'view' => null,
        'viewVar' => null,
        'serialize' => [],
        'api' => [
            'success' => [
                'code' => 200,
            ],
            'error' => [
                'code' => 400,
            ],
        ],
    ];

    /**
     * Generic handler for all HTTP verbs
     *
     * @param string $id Record id
     * @param string $associationName Association name
     * @return \Cake\Http\Response|void|null
     */
    protected function _handle(string $id, string $associationName)
    {
        $items = [];
        $subject = $this->_subject([
            'success' => true,
            'query' => $this->getQuery($id, $associationName),
        ]);

        $this->_trigger('beforePaginate', $subject);
        if (property_exists($subject, 'query')) {
            $items = $this->_controller()->paginate($subject->query);
        }

        $subject->set(['entities' => $items]);

        $this->_trigger('afterPaginate', $subject);
        $this->_trigger('beforeRender', $subject);
    }

    /**
     *
     * @param string $id Record id
     * @param string $associationName Association name
     * @return \Cake\Datasource\QueryInterface
     * @throws \InvalidArgumentException When reversed many-to-many association is not found
     */
    private function getQuery(string $id, string $associationName): QueryInterface
    {
        $association = $this->getAssociation($associationName);

        if (is_null($association)) {
            throw new InvalidArgumentException(
                sprintf('%s has no "%s" association', $this->_table()->getAlias(), $associationName)
            );
        }

        if (Association::MANY_TO_MANY === $association->type()) {
            return $this->manyToManyQuery($association, $id);
        }

        if (Association::ONE_TO_MANY === $association->type()) {
            return $this->oneToManyQuery($association, $id);
        }

        throw new InvalidArgumentException(
            sprintf('Association of type "%s" is not supported', $association->type())
        );
    }

    /**
     * Association instance getter.
     *
     * @param string $associationName associations name
     *
     * @return \Cake\ORM\Association|null
     */
    private function getAssociation(string $associationName): ?Association
    {
        foreach ($this->_table()->associations() as $association) {
            if ($association->getName() !== $associationName) {
                continue;
            }

            return $association;
        }

        return null;
    }

    /**
     * Method that generates many-to-many associations query
     *
     * @param \Cake\ORM\Association $association Association object
     * @param string $id Record id
     * @return \Cake\Datasource\QueryInterface
     * @throws \InvalidArgumentException When reversed many-to-many association is not found
     */
    private function manyToManyQuery(Association $association, string $id): QueryInterface
    {
        // pagination hack to modify alias
        $association->setTarget($association->getTarget());
        $association->getTarget()->setAlias($this->_controller()->getName());

        $related = $this->getManyToManyAssociation($association->getTarget(), $association->getName());
        if (is_null($related)) {
            throw new InvalidArgumentException(sprintf(
                '%s is not associated with %s',
                App::shortName(get_class($association->getTarget()), 'Model/Table', 'Table'),
                $this->_table()->getAlias()
            ));
        }

        $query = $association->find('all')->innerJoinWith($related->getName(), function ($q) use ($related, $id) {
            /** @var string $primaryKey */
            $primaryKey = $this->_table()->getPrimaryKey();

            return $q->where([$related->getTarget()->aliasField($primaryKey) => $id]);
        });

        return $query;
    }

    /**
     * Method that generates one-to-many associations query
     *
     * @param \Cake\ORM\Association $association Association object
     * @param string $id Record id
     * @return \Cake\Datasource\QueryInterface
     */
    private function oneToManyQuery(Association $association, string $id): QueryInterface
    {
        // pagination hack to modify alias
        $association->setTarget($association->getTarget());
        $association->getTarget()->setAlias($this->_controller()->getName());

        /** @var string $foreignKey */
        $foreignKey = $association->getForeignKey();

        $query = $association->find('all')->where([
            $association->getTarget()->aliasField($foreignKey) => $id,
        ]);

        return $query;
    }

    /**
     * Retrieves reversed many-to-many association instance, by matching
     * its class name with the current Controller's table class.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Association's table
     * @param string $associationName Association name
     * @return \Cake\ORM\Association|null
     */
    private function getManyToManyAssociation(RepositoryInterface $table, string $associationName): ?Association
    {
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $table;
        foreach ($table->associations() as $association) {
            if (Association::MANY_TO_MANY !== $association->type()) {
                continue;
            }

            if ($association->getClassName() !== $this->_table()->getAlias()) {
                continue;
            }

            // skip same association, fixes issue with self-associated tables
            if ($association->getName() === $associationName) {
                continue;
            }

            return $association;
        }

        return null;
    }
}
