<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserType;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/user/", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("signup", name="sign_up")
     */
    public function signUp(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $emi): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()):
          $user->setRoles(["ROLE_USER"])
              ->setPassword($encoder->encodePassword($user, $user->getPassword()))
              ->setPhoto('user.jpg');
          $emi->persist($user);
          $emi->flush();
          return $this->redirectToRoute('app_login');
        endif;
        return $this->render('user/sign_up.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
