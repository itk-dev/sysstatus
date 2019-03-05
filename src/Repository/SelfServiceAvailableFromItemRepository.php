<?php

namespace App\Repository;

use App\Entity\SelfServiceAvailableFromItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SelfServiceAvailableFromItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SelfServiceAvailableFromItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SelfServiceAvailableFromItem[]    findAll()
 * @method SelfServiceAvailableFromItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SelfServiceAvailableFromItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SelfServiceAvailableFromItem::class);
    }

    /**
     * Get an item by name. If it does not exist and new one will be created.
     *
     * @param string $name
     *
     * @return SelfServiceAvailableFromItem
     */
    public function getItem(string $name)
    {
        $item = $this->findOneBy(['name' => $name]);

        if (null === $item) {
            $item = new SelfServiceAvailableFromItem();
            $item->setName($name);
            $em = $this->getEntityManager();
            $em->persist($item);
            // Flush to make sure that we can find this item by name.
            $em->flush();
        }

        return $item;
    }
}
