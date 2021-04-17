<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\NewsLetterRepository;

use App\Entity\Letter;

use App\Form\LetterType;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Mailer\MailerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/omeraadmin/", name="admin_")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("", name="article_list")
     */
    public function index(Request $request, ArticleRepository $articleRepo): Response
    {
        $offset = max(0, $request->query->getInt('p', 0));
        $articlePaginator = $articleRepo->getPaginateArticles($offset);
        return $this->render('admin/index.html.twig', [
            'articles' => $articlePaginator,
            'previous' => $offset - ArticleRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($articlePaginator), $offset + ArticleRepository::PAGINATOR_PER_PAGE)
        ]);
    }

    /**
     * @Route("user/list", name="user_list")
     */

    public function userList(Request $request, UserRepository $userRepo)
    {
      $offset = max(0, $request->query->getInt('p', 0));
      $userPaginator = $userRepo->getPaginateUsers($offset);
      return $this->render('admin/user_list.html.twig', [
          'users' => $userPaginator,
          'previous' => $offset - UserRepository::PAGINATOR_PER_PAGE,
          'next' => min(count($userPaginator), $offset + UserRepository::PAGINATOR_PER_PAGE)
      ]);
    }

    /**
     * @Route("send-news-letter", name="send_news_letter")
     */
    public function sendNewsLetter(Request $request, NewsLetterRepository $newsletterRepo, EntityManagerInterface $emi, MailerInterface $mailer)
    {
      $targets = $newsletterRepo->findAll();
      $letter = new Letter();
      $form = $this->createForm(LetterType::class, $letter);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()):
        foreach ($targets as $target):
          $email = new TemplatedEmail();
          $email->from("bioabdelkarim47@gmail.com")
                ->to($target->getEmail())
                ->subject($letter->subject)
                ->htmlTemplate('email/news_letter_email_template.html.twig')
                ->context([
                  'sender' => "Omera Digital",
                  'message' => $letter->content
                ]);
          $mailer->send($email);
        endforeach;
        $this->addFlash("notice_success", "Votre newsletter a bien ete envoye a vos abonne(s)");
        return $this->redirectToRoute("admin_article_list");
      endif;
      return $this->render("admin/send_news_letter.html.twig", [
        'form' => $form->createView()
      ]);
    }
}
