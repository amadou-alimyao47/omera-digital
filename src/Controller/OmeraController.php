<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Mailer\MailerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Contact;
use App\Entity\Project;
use App\Entity\NewsLetter;

use App\Form\ContactType;

use App\Repository\ProjectRepository;
use App\Repository\ArticleRepository;
use App\Repository\ContributorRepository;

/**
 * @Route("/", name="omera_")
 */
class OmeraController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(ProjectRepository $projectRepo, ContributorRepository $contributorRepo, ArticleRepository $articleRepo): Response
    {
        return $this->render('omera/index.html.twig', [
            'projInDev' => $projectRepo->nbIn('dev'),
            'projInDev' => $projectRepo->nbIn('prod'),
            'satisfiedCustomer' => $projectRepo->nbCustomerSatisfied(),
            'members' => $contributorRepo->get4Members(),
            'article' => $articleRepo->findLasted(3)
        ]);
    }

    /**
     * @Route("works", name="works")
     */
    public function works(ProjectRepository $projectRepo, Request $request)
    {
      $opt = $request->query->get('opt', null);
      $works = ($opt)? $projectRepo->findAllAready() : $projectRepo->find8();

      return $this->render("omera/works.html.twig", [
        'works' => $works
      ]);
    }

    /**
     * @Route("work/show/{id}", name="work_show")
     */
    public function workShow(Project $project)
    {
      return $this->render("omera/work_show.html.twig", [
        'work' => $project
      ]);
    }

    /**
     * @Route("team", name="team")
     */
    public function team(ContributorRepository $contributorRepo)
    {
      return $this->render("omera/team.html.twig", [
        'team' => $contributorRepo->getTeam()
      ]);
    }

    /**
     * @Route("about-us", name="about_us")
     */
    public function aboutUs(ProjectRepository $projectRepo, ContributorRepository $contributorRepo)
    {
      return $this->render("omera/about_us.html.twig", [
        'projInDev' => $projectRepo->nbIn('dev'),
        'projInDev' => $projectRepo->nbIn('prod'),
        'satisfiedCustomer' => $projectRepo->nbCustomerSatisfied(),
        'members' => $contributorRepo->get4Members()
      ]);
    }

    /**
     * @Route("contact-us", name="contact_us")
     */
    public function contactUs(Request $request, EntityManagerInterface $emi, MailerInterface $mailer)
    {
      $contact = new Contact();
      $form = $this->createForm(ContactType::class, $contact);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()):
        $email = new TemplatedEmail();
        $email->from($contact->email)
              ->to("bioabdelkarim47@gmail.com")
              ->subject($contact->subject)
              ->htmlTemplate('email/contact_us_template.html.twig')
              ->context([
                'customer_name' => $contact->customer,
                'subject' => $contact->subject,
                'message' => $contact->challenge
              ]);
        $mailer->send($email);
        $project = (new Project())
                    ->setCustomer($contact->customer)
                    ->setEmail($contact->email)
                    ->setSubject($contact->subject)
                    ->setBudget($contact->budget)
                    ->setChallenge($contact->challenge)
                    ->setSolution('NO SOLUTION AT THE MOMENT')
                    ->setStatus('init')
                    ->setSatisfiedCustomer(false);
        $emi->persist($project);
        $emi->flush();
        $this->addFlash("notice_success", " Votre project a bien ete envoyer");
        return $this->redirectToRoute("omera_contact_us");
      endif;
      return $this->render("omera/contact_us.html.twig", [
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/add-to-news-letter", name="add_to_news_letter")
     */
    public function addToNewsLetter(Request $request, EntityManagerInterface $emi, MailerInterface $mailer)
    {
      if ($this->isCsrfTokenValid('news_letter', $request->request->get('_token'))):
        $email = $request->request->get('newsLetter');
        if(preg_match('#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#', $email)):
          $newsletter = new NewsLetter();
          $newsletter->setEmail($email);

          $emi->persist($newsletter);
          $emi->flush();
          $email = new TemplatedEmail();
          $email->from("bioabdelkarim47@gmail.com")
                ->to($newsletter->getEmail())
                ->subject("Welcome Omera Digital newsletter")
                ->htmlTemplate('email/welcome_newsletter.html.twig')
                ->context([
                  'subject' => "Welcome to the Omera Digital newsletter"
                ]);
          $mailer->send($email);
          $this->addFlash('notice_success', 'Vous avez bien ete ajoute a la newsletter vous recevrez les prochain email ');

        else:
          $this->addFlash("notice_newsletter_error", ' addresse mail invalide!');
        endif;
      endif;
      return $this->redirectToRoute("omera_index");
    }
}
