<?php

namespace App\Repository;

use App\Entity\Article;

use App\Repository\CategoryRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    const PAGINATOR_PER_PAGE = 9;
    /**
     * @var CategoryRepository
     */
    private $categoryRepo;

    public function __construct(ManagerRegistry $registry, CategoryRepository $categoryRepo)
    {
        parent::__construct($registry, Article::class);
        $this->categoryRepo = $categoryRepo;
    }

    public function getPaginateArticles(int $offset, string $searchByTitle = '', string $searchByCategory = ''):Paginator
    {
      //dd( $searchByTitle);
        $query = $this->createQueryBuilder('a');
        if ($searchByTitle != ''):
          $query = $query->orWhere('a.title LIKE :q')
                            ->setParameter('q', "%{$searchByTitle}%");
        endif;
        if ($searchByCategory != ''):
          $categories = $this->categoryRepo->findBy(['name' => $searchByCategory]);
          $query = $query->select('c', 'a')
                         ->join('a.categories', 'c')
                         ->orWhere('c.id IN (:categories)')
                         ->setParameter('categories', $categories);
        endif;
        $query = $query->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();
        return new Paginator($query);
    }

    public function findLasted(int $limit = 4)
    {
      return $this->createQueryBuilder('a')
          ->orderBy('a.id', 'DESC')
          ->setMaxResults($limit)
          ->getQuery()
          ->getResult()
      ;
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
