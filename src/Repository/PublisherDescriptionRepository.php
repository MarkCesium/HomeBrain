<?php

namespace App\Repository;

use App\Entity\PublisherDescription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublisherDescription>
 *
 * @method PublisherDescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublisherDescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublisherDescription[]    findAll()
 * @method PublisherDescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublisherDescriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublisherDescription::class);
    }

    /**
     * @param array $id
     * @param string $fieldGroup
     * @return mixed
     */
    public function getSensorsSettings(array $id, string $fieldGroup): mixed
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('pd', 'ps')
            ->from(PublisherDescription::class, 'pd')
            ->join('pd.publisherSetting', 'ps', 'pd.publisherSetting=ps.publisherDescription')
            ->where('pd.publisher in (:ids)')
            ->andWhere('ps.fieldsGroup = :fieldsGroup')
            ->setParameter('ids', $id)
            ->setParameter('fieldsGroup', $fieldGroup)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $id
     * @param string $fieldGroup
     * @return mixed
     */
    public function getSensorSettings(int $id, string $fieldGroup): mixed
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('pd', 'ps')
            ->from(PublisherDescription::class, 'pd')
            ->join('pd.publisherSetting', 'ps', 'pd.publisherSetting=ps.publisherDescription')
            ->where('pd.publisher = :id')
            ->andWhere('ps.fieldsGroup = :fieldsGroup')
            ->setParameter('id', $id)
            ->setParameter('fieldsGroup', $fieldGroup)
            ->getQuery();

        return $query->getResult();
    }
//    /**
//     * @return PublisherDescription[] Returns an array of PublisherDescription objects
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

//    public function findOneBySomeField($value): ?PublisherDescription
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
