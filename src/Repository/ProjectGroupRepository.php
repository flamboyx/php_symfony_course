<?php

namespace App\Repository;

use App\Entity\ProjectGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectGroup>
 *
 * @method ProjectsGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectsGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectsGroup[]    findAll()
 * @method ProjectsGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectGroup::class);
    }

    // /**
    //  * @return ProjectGroup[] Returns an array of ProjectGroup objects
    //  */
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('p.id', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // /**
    //  * @return ProjectGroup|null Returns a single ProjectGroup object
    //  */
    // public function findOneBySomeField($value): ?ProjectGroup
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}

