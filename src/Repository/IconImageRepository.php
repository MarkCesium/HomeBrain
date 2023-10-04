<?php

namespace App\Repository;

use App\Entity\IconImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IconImage>
 *
 * @method IconImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method IconImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method IconImage[]    findAll()
 * @method IconImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IconImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IconImage::class);
    }

//    /**
//     * @return IconImage[] Returns an array of IconImage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IconImage
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
