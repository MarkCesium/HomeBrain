<?php

namespace App\Repository;

use App\Entity\Location;
use App\Entity\Publisher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 *
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findUserLocations(int $userId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('l')
            ->from(Location::class, 'l')
            ->leftJoin('l.userLocations', 'ul', 'l.userLocations=ul.location')
            ->where('ul.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('l.id', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $locationId
     * @param int $userId
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserLocation(int $locationId, int $userId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('l')
            ->from(Location::class, 'l')
            ->leftJoin('l.userLocations', 'ul', 'l.userLocation=ul.location')
            ->leftJoin('ul.user', 'u', 'ul.user=u.id')
            ->where('ul.user = :userId AND l.id = :locationId')
            ->setParameter('userId', $userId)
            ->setParameter('locationId', $locationId)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findUserPublishers(int $locationId, int $userId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from(Publisher::class, 'p')
            ->leftJoin('p.location', 'l', 'p.location=l.id')
            ->leftJoin('l.userLocations', 'ul', 'l.userLocations=ul.location')
            ->where('ul.user = :userId AND l.id = :locationId')
            ->setParameter('userId', $userId)
            ->setParameter('locationId', $locationId)
            ->getQuery();

        return $query->getResult();
    }

    public function getLocationPublishers(int $locationId)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from(Publisher::class, 'p')
            ->leftJoin('p.location', 'l', 'p.location=l.id')
            ->where('l.id = :locationId')
            ->setParameter('locationId', $locationId)
            ->getQuery();

        return $query->getResult();
    }

//    /**
//     * @return Location[] Returns an array of Location objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Location
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
