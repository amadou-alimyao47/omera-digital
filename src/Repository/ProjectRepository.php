<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{

    const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function find8()
    {
        return $this->createQueryBuilder('p')
            ->where('p.satisfiedCustomer = :value')
            ->setParameter('value', true)
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult()
        ;
    }

    public function nbIn(string $level)
    {
        $nb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :val')
            ->setParameter('val', $level)
            ->getQuery()
            ->getResult();
        return count($nb);
    }

    public function nbCustomerSatisfied()
    {
        $nb = $this->createQueryBuilder('p')
            ->andWhere('p.satisfiedCustomer = :val')
            ->setParameter('val', true)
            ->getQuery()
            ->getResult();
        return count($nb);
    }

    public function getPaginateProjects(int $offset, string $state = ''):Paginator
    {
        $query = $this->createQueryBuilder('p');
        if ($state):
          $query = $query->where('p.status = :state')
                         ->setParameter('state', $state);
        endif;
        $query = $query->orderBy('p.id', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;
        return new Paginator($query);
    }

    public function findAllAready()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.satisfiedCustomer = :val')
            ->setParameter('val', true)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return Project[] Returns an array of Project objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
