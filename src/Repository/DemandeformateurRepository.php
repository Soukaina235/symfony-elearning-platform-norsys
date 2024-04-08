<?php

namespace App\Repository;

use App\Entity\Demandeformateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demandeformateur>
 *
 * @method Demandeformateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demandeformateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demandeformateur[]    findAll()
 * @method Demandeformateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeformateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demandeformateur::class);
    }

    public function add(Demandeformateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Demandeformateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Demandeformateur[] Returns an array of Demandeformateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Demandeformateur
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
