<?php

namespace App\Repository;

use App\Entity\ImportRun;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ImportRun|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportRun|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportRun[]    findAll()
 * @method ImportRun[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportRunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportRun::class);
    }
}
