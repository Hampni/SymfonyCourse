<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\ProductRepository;
class ProductsController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function homepage(ProductRepository $repo) : Response
    {

        $bikes = $repo->findBy([]);
        return $this->render('homepage.html.twig',[
            'date' => date('l'),
            'bikes' => $bikes
        ]);
    }

    /**
     * @Route("/products/{id}")
     */
    public function details($id,Request $request, SessionInterface $session, ProductRepository $repo): Response
    {

        $bike = $repo->find($id);
        if ($bike === null) {
            throw $this->createNotFoundException('The product does not exists!');
        }

        // add to basket handling
        $basket = $session->get('basket', []);

        if ($request->isMethod('POST')) {
            $basket[$bike->getId()] = $bike;
            $session->set('basket', $basket);
        }
        $isInBasket = array_key_exists($bike->getId(), $basket);

        return $this->render('details.html.twig', [
            'bike' => $bike,
            'isInBasket' => $isInBasket
        ]);
    }

}