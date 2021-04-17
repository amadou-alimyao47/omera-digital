<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;

use App\Entity\Article;
use App\Entity\Comment;

use App\Form\CommentType;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/blog/", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(ArticleRepository $articleRepo, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('p', 0));
        $searchByTitle = $request->query->get('q', '');
        $searchByCategory = $request->query->get('category', '');
        $cat = ''; $q = '';
        if ($searchByTitle != ''):
          $q = $searchByTitle;
        elseif($searchByCategory != ''):
          $cat = $searchByCategory;
        endif;
        $articlePaginator = $articleRepo->getPaginateArticles($offset, $searchByTitle, $searchByCategory);
      //  dd($articlePaginator);
        return $this->render('blog/index.html.twig', [
            'articles' => $articlePaginator,
            'previous' => $offset - ArticleRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($articlePaginator), $offset + ArticleRepository::PAGINATOR_PER_PAGE),
            'category' => $cat,
            'q' =>$q
        ]);

    }

    /**
     * @Route("show/{id}-{slug}", name="show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $emi, CommentRepository $commentRepo, ArticleRepository $articleRepo, CategoryRepository $categoryRepo)
    {
      $offset = max(0, $request->query->getInt('p', 0));
      $commentPaginator = $commentRepo->getPaginateComments($offset, $article);

      $comment = new Comment();
      $form = $this->createForm(CommentType::class, $comment);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()):
        $comment->setArticle($article)
                ->setAuthor($this->getUser())
                ->setCreatedAt(new \DateTime());
        $emi->persist($comment);
        $emi->flush();
        return $this->redirectToRoute("blog_show", [
          'id' => $article->getId(),
          'slug' => $article->getSlug()
        ]);
      endif;
      return $this->render("blog/show.html.twig", [
        'article' => $article,
        'form' => $form->createView(),
        'comments' => $commentPaginator,
        'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
        'next' => min(count($commentPaginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
        'search' => null,
        'lastedArticles' => $articleRepo->findLasted(),
        'categories' => $categoryRepo->findAll()
      ]);
    }
}
