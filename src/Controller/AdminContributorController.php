<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ContributorRepository;

use App\Entity\Contributor;

use App\Form\ContributorType;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/admin/contributor/", name="admin_contributor_")
 */
class AdminContributorController extends AbstractController
{
    /**
     * @Route("list", name="list")
     */
    public function index(ContributorRepository $contributorRepo, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('p', 0));
        $type = $request->query->get("q", null);
        $contributorPaginator = $contributorRepo->getPaginateContributors($offset, $type);
        return $this->render('admin_contributor/index.html.twig', [
            'contributors' => $contributorPaginator,
            'previous' => $offset - ContributorRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($contributorPaginator), $offset + ContributorRepository::PAGINATOR_PER_PAGE)

        ]);

    }

    /**
     * @Route("new", name="new")
     * @Route("update-{id}", name="update")
     */
    public function save(Contributor $contributor = null, Request $request, EntityManagerInterface $emi)
    {
      $contributor = ($contributor)? $contributor : new Contributor();
      $form = $this->createForm(ContributorType::class, $contributor);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()):
        if($photo = $form['photoImage']->getData()):
          $filename =  md5(uniqid()) . '.' . $photo->guessExtension();
          try {
            $photo->move( $this->getParameter('images_contributor_directory'), $filename);
          } catch (FileException $e) {
              echo $e->getMessage();
          }
          if($contributor->getPhoto()):
            unlink($this->getParameter('images_contributor_directory').'/'.$contributor->getPhoto());
          endif;
          $contributor->setPhoto($filename);
        elseif (!$form['photoImage']->getData() && !$contributor->getPhoto()):
          $contributor->setPhoto("img/contributor/dev.jpg");
        endif;
        $emi->persist($contributor);
        $emi->flush();
        $notif = ($contributor->getIsMember())? $contributor->getFullname(). " a bien ete ajoute aux membre d'Omera!" : $contributor->getFullname(). " a bien ete ajoute aux contributeurs d'Omera";
        $this->addFlash("notice_success", $notif);
        return $this->redirectToRoute("admin_contributor_list");
      endif;
      return $this->render("admin_contributor/save.html.twig", [
        'form' => $form->createView(),
        'contributor' => $contributor
      ]);
    }

    /**
     * @Route("delete-{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Contributor $contributor, Request $request)
    {
      if ($this->isCsrfTokenValid('contributor_delete'.$contributor->getId(), $request->request->get('_token'))) {
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->remove($contributor);
          $entityManager->flush();
          unlink($this->getParameter('images_contributor_directory').'/'.$contributor->getPhoto());
          $this->addFlash("notice_success", "Le membre ". $contributor->getFullname() . " a bien ete supprime!");
      }

      return $this->redirectToRoute('admin_contributor_list');
    }

}
