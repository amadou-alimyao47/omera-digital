<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Image;
use App\Entity\Article;

use App\Form\ArticleType;

/**
 * @Route("/admin/article/", name="admin_article_")
 */

class AdminArticleController extends AbstractController
{
    /**
    * @Route("/new", name="new")
     * @Route("/update-{id}-{slug}", name="update")
     */
    public function save(Request $request, EntityManagerInterface $emi, Article $article = null): Response
    {
        $article = ($article)? $article : new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()):
          $pictures = $form->get("pictures")->getData();
          foreach($pictures as $picture):
                $file = md5(uniqid()) . '.' . $picture->guessExtension();
                // On copie le fichier dans le dossier uploads
                $picture->move(
                    $this->getParameter('images_article_directory'),
                    $file
                );

                // On stocke l'image dans la base de données (son nom)
                $img = new Image();
                $img->setUrl($file);
                $article->addPicture($img);
          endforeach;
          $article->setSlug(str_replace(' ', '-', $article->getTitle()));
          $emi->persist($article);
          $emi->flush();
          return $this->redirectToRoute('admin_article_list');
        endif;
        return $this->render('admin_article/save.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("delete-{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('article_delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash("notice_success", "L'article ". $article->getTitle() . " a bien ete supprime!");
        }

        return $this->redirectToRoute('admin_article_list');
    }

    /**
     * @Route("picture/delete/{id}", name="picture_delete")
     */
    public function articleDeletePicture(Image $picture, Request $request)
    {

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('art_del_img'.$picture->getId(), $request->query->get('tk'))){
            // On récupère le nom de l'image
            $imgName = $picture->getUrl();
            // On supprime le fichier
            unlink($this->getParameter('images_article_directory').'/'.$imgName);

            // On supprime l'entrée de la base de données
            $em = $this->getDoctrine()->getManager();
            $em->remove($picture);
            $em->flush();
            $this->addFlash("notice_success", "Image Supprimer avec sucess!");
        }else{
            $this->addFlash("notice_error", "Error de suppression ou token invalid");
        }
        return $this->redirectToRoute("admin_article_update", [
            'id'   => $picture->getArticle()->getId(),
            'slug' => $picture->getArticle()->getSlug()
          ]);
    }
}
