<?php

namespace App\Repository;

use App\Entity\UserLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLocation>
 *
 * @method UserLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLocation[]    findAll()
 * @method UserLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLocation::class);
    }

//    /**
//     * @return UserLocation[] Returns an array of UserLocation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserLocation
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
