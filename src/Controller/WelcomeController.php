<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/first")
     */
    public function homepage() : Response
    {

        return $this->render('welcome.html.twig',[
            'date' => date('l')
        ]);
    }

}