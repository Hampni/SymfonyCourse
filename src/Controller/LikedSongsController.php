<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\AlbumsRepository;
use App\Repository\ArtistsRepository;
use App\Repository\SongsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class LikedSongsController extends AbstractController
{

    /**
     * @Route("/liked", name="likedSongs")
     */
    public function homepage(Request $request, UserRepository $userRepository, SongsRepository $songsRepository): Response
    {
        //user
        $user = $userRepository->find($this->getUser()->getId());

        //get array of songs (objects)
        $songsArray = $user->getSongs()->getValues();

        return $this->render('likedTracks.html.twig', [
            'likedSongs' => $songsArray
        ]);
    }

}