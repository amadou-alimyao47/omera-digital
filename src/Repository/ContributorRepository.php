<?php

namespace App\Repository;

use App\Entity\Contributor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @method Contributor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contributor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contributor[]    findAll()
 * @method Contributor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContributorRepository extends ServiceEntityRepository
{

    const PAGINATOR_PER_PAGE = 12;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contributor::class);
    }

    public function getPaginateContributors(int $offset, string $type = null):Paginator
    {
        $query = $this->createQueryBuilder('c');
        if ($type):
          $query = $query->where('c.isMember = :b')
                         ->setParameter('b', true);
        endif;
        $query = $query->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;
        return new Paginator($query);
    }

    public function getTeam()
    {
      return $this->createQueryBuilder('c')
          ->andWhere('c.isMember = :val')
          ->setParameter('val', true)
          ->orderBy('c.id', 'DESC')
          ->getQuery()
          ->getResult()
      ;
    }

    public function get4Members()
    {
      return $this->createQueryBuilder('c')
          ->andWhere('c.isMember = :val')
          ->setParameter('val', true)
          ->orderBy('c.id', 'DESC')
          ->setMaxResults(4)
          ->getQuery()
          ->getResult()
      ;
    }
    // /**
    //  * @return Contributor[] Returns an array of Contributor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contributor
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
