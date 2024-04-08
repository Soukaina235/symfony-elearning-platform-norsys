<?php

namespace App\Repository;

use App\Entity\RessourceUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RessourceUser>
 *
 * @method RessourceUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method RessourceUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method RessourceUser[]    findAll()
 * @method RessourceUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RessourceUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RessourceUser::class);
    }

    public function findOneByRessourceAndUser($ressourceid, $userid): ?RessourceUser
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.ressourceid = :ressourceId')
            ->andWhere('r.userid = :userId')
            ->setParameter('ressourceId', $ressourceid)
            ->setParameter('userId', $userid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return RessourceUser[] Returns an array of RessourceUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RessourceUser
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
