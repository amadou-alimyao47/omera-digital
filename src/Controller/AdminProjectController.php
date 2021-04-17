<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ProjectRepository;

use App\Entity\Project;

use App\Form\ProjectType;

use Doctrine\ORM\EntityManagerInterface;


/**
 * @Route("/admin/project/", name="admin_project_")
 */
class AdminProjectController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function index(ProjectRepository $projectRepo, Request $request): Response
    {
        $offset = max(0, $request->query->getInt('p', 0));
        $state = $request->query->get('state', '');
        $projectPaginator = $projectRepo->getPaginateProjects($offset, $state);
        return $this->render('admin_project/index.html.twig', [
            'projects' => $projectPaginator,
            'previous' => $offset - ProjectRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($projectPaginator), $offset + ProjectRepository::PAGINATOR_PER_PAGE),
            'state' => $state
        ]);

    }

    /**
     * @Route("update-{id}-{subject}", name="update")
     */
    public function update(Request $request, Project $project, EntityManagerInterface $emi)
    {
      $form = $this->createForm(ProjectType::class, $project);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()):
      endif;
      return $this->render("admin_project/edit.html.twig", [
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("see-more-{id}-{subject}", name="see_more")
     */
    public function seeMore(Project $project)
    {
      return $this->render("admin_project/see_more.html.twig", [
        'project' => $project
      ]);
    }



}
