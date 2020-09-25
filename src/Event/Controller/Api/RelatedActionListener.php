<?php

namespace App\Event\Controller\Api;

use App\Event\EventName;
use App\ORM\PrettyFormatter;
use App\ORM\RawFormatter;
use Cake\Controller\Controller;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Webmozart\Assert\Assert;

class RelatedActionListener extends BaseActionListener
{
    /**
     * Returns a list of all events that the API Related endpoint will listen to.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            (string)EventName::API_RELATED_BEFORE_PAGINATE() => 'beforePaginate',
            (string)EventName::API_RELATED_AFTER_PAGINATE() => 'afterPaginate',
            (string)EventName::API_RELATED_BEFORE_RENDER() => 'beforeRender',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function beforePaginate(Event $event, QueryInterface $query): void
    {
        $controller = $event->getSubject();
        Assert::isInstanceOf($controller, Controller::class);

        $request = $controller->getRequest();

        $table = $controller->loadModel();
        Assert::isInstanceOf($table, Table::class);

        $query->order($this->getOrderClause($request, $table));

        $prettyFormat = static::FORMAT_PRETTY === $request->getQuery('format');

        Assert::isInstanceOf($query, Query::class);
        $query->formatResults($prettyFormat ? new PrettyFormatter() : new RawFormatter());
    }

    /**
     * {@inheritDoc}
     */
    public function afterPaginate(Event $event, ResultSetInterface $resultSet): void
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event, ResultSetInterface $resultSet): void
    {
        if ($resultSet->isEmpty()) {
            return;
        }

        $controller = $event->getSubject();
        Assert::isInstanceOf($controller, Controller::class);

        $request = $controller->getRequest();

        $table = $controller->loadModel();
        Assert::isInstanceOf($table, Table::class);

        // Associated table instance.
        $target = $table->getAssociation($request->getParam('pass.1'))->getTarget();

        foreach ($resultSet as $entity) {
            if (static::FORMAT_PRETTY !== $request->getQuery('format')) {
                $this->attachFiles($entity, $target);
            }

            if ((bool)$request->getQuery(static::FLAG_INCLUDE_MENUS)) {
                $this->attachRelatedMenu($entity, $target, $controller->Auth->user(), [
                    'associationController' => $request->getParam('controller'),
                    'associationName' => $target->getRegistryAlias(),
                    'associationId' => $request->getParam('pass.0'),
                ]);
            }
        }
    }
}
