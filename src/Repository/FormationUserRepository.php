<?php

namespace App\Repository;

use App\Entity\FormationUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormationUser>
 *
 * @method FormationUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormationUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormationUser[]    findAll()
 * @method FormationUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationUser::class);
    }

//    /**
//     * @return FormationUser[] Returns an array of FormationUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FormationUser
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
