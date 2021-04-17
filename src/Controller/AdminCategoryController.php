<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\CategoryRepository;

use App\Entity\Category;

use App\Form\CategoryType;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/admin/categories", name="admin_category_")
 */
class AdminCategoryController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function index(CategoryRepository $categoryRepo, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('p', 0));
        $categoryPaginator = $categoryRepo->getPaginateCategories($offset);
        return $this->render('admin_category/index.html.twig', [
            'categories' => $categoryPaginator,
            'previous' => $offset - CategoryRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($categoryPaginator), $offset + CategoryRepository::PAGINATOR_PER_PAGE)

        ]);
    }

    /**
     * @Route("/new", name="new")
     * @Route("/update-{id}", name="update")
     */
    public function save(Category $category = null, Request $request, EntityManagerInterface $emi)
    {
      $category = ($category)? $category : new Category();
      $form = $this->createForm(CategoryType::class, $category);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()):
        $emi->persist($category);
        $emi->flush();
        $this->addFlash("notice_success", "L a category ". $category->getName() . " a bien ete enregistrer!");
        return  $this->redirectToRoute("admin_category_list");
      endif;
      return $this->render("admin_category/save.html.twig", [
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/delete-{id}", name="delete")
     */
    public function delete(Category $category, Request $request)
    {
          if ($this->isCsrfTokenValid('category_delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash("notice_success", "La categorie ". $category->getName() . " a bien ete supprime!");
        }

        return $this->redirectToRoute('admin_category_list');
    }


}
