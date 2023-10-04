<?php

namespace App\Repository;

use App\Entity\PublisherSetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublisherSetting>
 *
 * @method PublisherSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublisherSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublisherSetting[]    findAll()
 * @method PublisherSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublisherSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublisherSetting::class);
    }

//    /**
//     * @return PublisherSetting[] Returns an array of PublisherSetting objects
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

//    public function findOneBySomeField($value): ?PublisherSetting
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
