<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\AlbumsRepository;
use App\Repository\ArtistsRepository;
use App\Repository\SongsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomepageController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(Request $request, ArtistsRepository $artistsRepository, SongsRepository $songsRepository): Response
    {
        return $this->render('homepage.html.twig');
    }

    public function allSongs(SongsRepository $songsRepository, AlbumsRepository $albumsRepository, ArtistsRepository $artistsRepository): Response
    {

        $albumsId = [];
        $authorsId = [];
        $artists = [];
        $albums = [];

        $allSongs = $songsRepository->findBy(['name' => '']);

        if (!empty($_POST['allSearch'])) {
            $allSongs = $songsRepository->findBy(['name' => $_POST['allSearch']]);
        }
        //получение айди авторов
        foreach ($allSongs as $song) {
            $authorsId[] = $song->getAuthorId();
        }
        //получение айди альбомов
        foreach ($allSongs as $song) {
            $albumsId[] = $song->getAlbumId();
        }

        foreach ($albumsId as $albumId) {
            $albums[] = $albumsRepository->find(['id' => $albumId]);
        }

        //получение исполнителей
        foreach ($authorsId as $authorId) {
            $artists[] = $artistsRepository->find(['id' => $authorId]);
        }


        return $this->render('recent_list.html.twig', [
                'allSongs' => $allSongs,
                'artists' => $artists,
                'albums' => $albums
            ]
        );
    }

    public function SearchRequest()
    {
        if (!empty($_POST['allSearch'])) {
            $postRequest = $_POST['allSearch'];
        } else {
            $postRequest = '';
        }

        return $this->render('searchRequest.html.twig', [
            'searchRequest' => $postRequest
        ]);
    }




}