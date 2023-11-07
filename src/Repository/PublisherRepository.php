<?php

namespace App\Repository;

use App\Entity\Publisher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publisher>
 *
 * @method Publisher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publisher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publisher[]    findAll()
 * @method Publisher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublisherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publisher::class);
    }

    /**
     * @param int $userId
     * @param int $type
     * @return int|mixed|string
     */
    public function findUserPublishers(int $userId, int $type)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p', 'pd', 'ps')
            ->from(Publisher::class, 'p')
            ->leftJoin('p.location', 'l', 'p.location=l.id')
            ->leftJoin('l.userLocations', 'ul', 'l.userLocations=ul.location')
            ->leftJoin('p.publisherDescriptions', 'pd', 'p.publisherDescriptions=pd.publisher')
            ->leftJoin('pd.publisherSetting', 'ps', 'pd.publisherSetting=ps.publisherDescription')
            ->where('ul.user = :userId')
            ->andWhere('p.type = :publisherType')
            ->setParameter('userId', $userId)
            ->setParameter('publisherType', $type)
            ->getQuery();

        return $query->getResult();
    }

    public function findUserPublisher(int $userId, int $publisherId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p', 'pd', 'ps')
            ->from(Publisher::class, 'p')
            ->leftJoin('p.location', 'l', 'p.location=l.id')
            ->leftJoin('l.userLocations', 'ul', 'l.userLocations=ul.location')
            ->leftJoin('p.publisherDescriptions', 'pd', 'p.publisherDescriptions=pd.publisher')
            ->leftJoin('pd.publisherSetting', 'ps', 'pd.publisherSetting=ps.publisherDescription')
            ->where('ul.user = :userId')
            ->andWhere('p.id = :publisherId')
            ->setParameter('userId', $userId)
            ->setParameter('publisherId', $publisherId)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

//    /**
//     * @return Publisher[] Returns an array of Publisher objects
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

//    public function findOneBySomeField($value): ?Publisher
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
