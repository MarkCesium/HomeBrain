<?php

namespace App\Repository;

use App\Entity\Publisher;
use App\Entity\UserApi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserApi>
 *
 * @method UserApi|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserApi|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserApi[]    findAll()
 * @method UserApi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserApiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserApi::class);
    }

    public function findUserApiPublishers(int $userApiId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from(Publisher::class, 'p')
            ->leftJoin('p.location', 'l', 'p.location=l.id')
            ->leftJoin('l.UserApi', 'ua', 'l.UserApi=ua.id')
            ->where('ua.id = :userApiId')
            ->setParameter('userApiId', $userApiId)
            ->getQuery();

        return $query->getResult();
    }

//    /**
//     * @return UserApi[] Returns an array of UserApi objects
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

//    public function findOneBySomeField($value): ?UserApi
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
