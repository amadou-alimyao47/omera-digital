<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/omera/service/", name="omera_service_")
 */
class OmeraServiceController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('omera_service/index.html.twig', [
            'controller_name' => 'OmeraServiceController',
        ]);
    }

    /**
     * @Route("digital-media", name="digital_media")
     */
    public function digitalMedia(): Response
    {
        return $this->render('omera_service/digital_media.html.twig');
    }

    /**
     * @Route("creative-design", name="creative_design")
     */
    public function creativeDesign(): Response
    {
        return $this->render('omera_service/creative_design.html.twig');
    }

    /**
     * @Route("development", name="development")
     */
    public function development(): Response
    {
        return $this->render('omera_service/development.html.twig');
    }

    /**
     * @Route("branding-strategy", name="branding_strategy")
     */
    public function brandingStrategy(): Response
    {
        return $this->render('omera_service/branding_strategy.html.twig');
    }
}
