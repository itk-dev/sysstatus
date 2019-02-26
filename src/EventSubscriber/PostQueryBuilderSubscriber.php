<?php

namespace App\EventSubscriber;

use App\Entity\System;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class PostQueryBuilderSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
          EasyAdminEvents::POST_LIST_QUERY_BUILDER => ['onPostListQueryBuilder'],
          EasyAdminEvents::POST_SEARCH_QUERY_BUILDER => ['onPostSearchQueryBuilder'],
        );
    }

    /**
     * Called on POST_LIST_QUERY_BUILDER event.
     *
     * @param GenericEvent $event
     */
    public function onPostListQueryBuilder(GenericEvent $event)
    {
        $queryBuilder = $event->getArgument('query_builder');

        if ($event->hasArgument('request')) {
            $this->applyRequestFilters($queryBuilder, $event->getArgument('request')->get('filters', []));
        }
    }

    /**
     * Called on POST_SEARCH_QUERY_BUILDER event.
     *
     * @param GenericEvent $event
     */
    public function onPostSearchQueryBuilder(GenericEvent $event)
    {
        $queryBuilder = $event->getArgument('query_builder');

        if ($event->hasArgument('request')) {
            $this->applyRequestFilters($queryBuilder, $event->getArgument('request')->get('filters', []));
        }
    }

    /**
     * Applies filters on queryBuilder.
     *
     * @param QueryBuilder $queryBuilder
     * @param array        $filters
     */
    protected function applyRequestFilters(QueryBuilder $queryBuilder, array $filters = array())
    {
        // Apply filter on selfServiceAvailableFromItems
        // (cf. https://github.com/alterphp/EasyAdminExtensionBundle/issues/93)
        // @TODO: Generalize this to filter on any ManyToMany relations.
        $entities = $queryBuilder->getRootEntities();
        if (1 === \count($entities) && System::class === $entities[0]
          && !empty($filters['selfServiceAvailableFromItems'])) {
            $ids = (array)$filters['selfServiceAvailableFromItems'];
            $alias = $queryBuilder->getRootAliases()[0];
            foreach ($ids as $index => $id) {
                $parameterName = 'selfServiceAvailableFromItems'.$index;
                $queryBuilder->andWhere(':'.$parameterName.' MEMBER OF '.$alias.'.selfServiceAvailableFromItems')
                  ->setParameter($parameterName, $id);
            }
        }
    }
}
