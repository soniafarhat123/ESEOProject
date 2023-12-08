<?php

namespace App\Repository;

use App\Entity\Analyse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Analyse>
 *
 * @method Analyse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Analyse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Analyse[]    findAll()
 * @method Analyse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analyse::class);
    }

    public function save(Analyse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Analyse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function selectNom()
    {
        return $this->createQueryBuilder('a')
            ->select('a.nomImage')
            ->orderBy('a.nomImage','DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function selectResultat()
    {
        return $this->createQueryBuilder('a')
            ->select('a.resultat')
            ->orderBy('a.resultat','DESC')
            ->getQuery()
            ->getResult()
            ;
    }
//    /**
//     * @return Analyse[] Returns an array of Analyse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Analyse
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
