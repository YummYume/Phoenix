<?php

namespace App\Repository;

use App\Entity\Milestone;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Milestone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Milestone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Milestone[]    findAll()
 * @method Milestone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MilestoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Milestone::class);
    }

    // get all the milestones where the given user is a member or the responsible of the team
    public function getMilestonesByUser(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('m');

        $qb
            ->join('m.project', 'p')
            ->join('p.team', 't')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('t.responsible', ':user'),
                $qb->expr()->isMemberOf(':user', 't.members')
            ))
            ->orderBy('m.position', 'ASC')
            ->setParameter('user', $user)
        ;

        return $qb;
    }

    // get all the milestones of the current project
    public function getMilestonesByProject(Project $project): QueryBuilder
    {
        return
            $this->createQueryBuilder('m')
            ->where('m.project = :project')
            ->orderBy('m.position', 'ASC')
            ->setParameter('project', $project)
        ;
    }
}
