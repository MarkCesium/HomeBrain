<?php

namespace App\Repository;

use App\Entity\PublisherValueArchieve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublisherValueArchieve>
 *
 * @method PublisherValueArchieve|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublisherValueArchieve|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublisherValueArchieve[]    findAll()
 * @method PublisherValueArchieve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublisherValueArchieveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublisherValueArchieve::class);
    }

//    /**
//     * @return PublisherValueArchieve[] Returns an array of PublisherValueArchieve objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PublisherValueArchieve
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
