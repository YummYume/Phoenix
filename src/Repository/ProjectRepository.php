<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    // get all public projects with title or description like query
    public function getAllProjects(?string $query = null, ?User $user = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
        ;

        if (!$user) {
            $qb
                ->join('p.team', 't')
                ->join('p.clientTeam', 'ct')
                ->where($qb->expr()->orX(
                    'p.private = false',
                    ':user = t.responsible',
                    ':user = ct.responsible',
                    $qb->expr()->isMemberOf(':user', 't.members'),
                    $qb->expr()->isMemberOf(':user', 'ct.members')
                ))
                ->setParameter('user', $user)
            ;
        }

        if ($query) {
            $qb
                ->andWhere($qb->expr()->orX(
                    'p.name LIKE :query',
                    'p.description LIKE :query'
                ))
                ->setParameter('query', '%'.$query.'%')
            ;
        }

        return $qb;
    }

    public function getProjectsByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->join('p.team', 't')
            ->where(':user MEMBER OF t.members')
            ->setParameter('user', $user)
        ;
    }

    // get all the active projects of a given user
    public function getActiveProjectsByUser(User $user, bool $withRisks = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->join('p.team', 't')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('t.responsible', ':user'),
                $qb->expr()->isMemberOf(':user', 't.members')
            ))
            ->andWhere('p.endAt > :now')
            ->andWhere('p.startAt < :now')
            ->andWhere('p.archived = false')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
        ;

        if ($withRisks) {
            $qb
                ->leftJoin('p.risks', 'r')
                ->andWhere('r.id IS NOT NULL')
            ;
        }

        return $qb;
    }

    // get all the upcoming projects of a given user
    public function getUpcomingProjectsByUser(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->join('p.team', 't')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('t.responsible', ':user'),
                $qb->expr()->isMemberOf(':user', 't.members')
            ))
            ->andWhere('p.startAt > :now')
            ->andWhere('p.archived = false')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
        ;
    }
}
