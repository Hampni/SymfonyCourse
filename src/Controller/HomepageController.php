<?php

namespace App\Controller;


use App\Repository\ArtistsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function homepage(ArtistsRepository $artistsRepository): Response
    {
        return $this->render('homepage.html.twig');
    }

}