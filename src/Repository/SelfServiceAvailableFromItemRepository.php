<?php

namespace App\Repository;

use App\Entity\SelfServiceAvailableFromItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SelfServiceAvailableFromItem>
 *
 * @method SelfServiceAvailableFromItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SelfServiceAvailableFromItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SelfServiceAvailableFromItem[]    findAll()
 * @method SelfServiceAvailableFromItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SelfServiceAvailableFromItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SelfServiceAvailableFromItem::class);
    }

    public function save(SelfServiceAvailableFromItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SelfServiceAvailableFromItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SelfServiceAvailableFromItem[] Returns an array of SelfServiceAvailableFromItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SelfServiceAvailableFromItem
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
