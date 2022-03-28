<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    // get all the teams of a user
    public function findAllByUser(User $user): QueryBuilder
    {
        $qb = ($this->createQueryBuilder('t'));

        $qb
            ->where($qb->expr()->orX(
                $qb->expr()->isMemberOf(':user', 't.members'),
                't.responsible = :user'
            ))
            ->setParameter('user', $user)
            ->orderBy('t.name', 'ASC')
        ;

        return $qb;
    }
}
