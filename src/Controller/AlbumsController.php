<?php

namespace App\Controller;


use App\Repository\AlbumsRepository;
use App\Repository\ArtistsRepository;
use App\Repository\SongsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumsController extends AbstractController
{

    /**
     * @Route("/artists")
     */
    public function artists(ArtistsRepository $artistsRepository): Response
    {

        $artists = $artistsRepository->findBy([]);


        foreach ($artists as $artist) {
            $exploded = explode(' ', $artist->getName());
            $imploded = implode('_', $exploded);

            $artistsLinks[$artist->getName()] = $imploded;

        }

        return $this->render('artists.html.twig', [
                'artists' => $artists,
                'links' => $artistsLinks,
            ]
        );
    }

    /**
     * @Route("/artists/{id}")
     */
    public function artistDetails($id, ArtistsRepository $artistsRepository, AlbumsRepository $albumsRepository,
                                  SongsRepository $songsRepository): Response
    {


        $artist = $artistsRepository->find($id);
        $albums = $albumsRepository->findBy(['authorId' => $artistsRepository->find($id)]);
        $music = [];
        foreach ($albums as $index => $album) {
        $music[$album->getId()] = $songsRepository->findBy(['albumId' => $album->getId()]);
        };
       // $music =  $songsRepository->findBy(['albumId' => [1,2]]);
        return $this->render('artistDetails.html.twig', [
            'artist' => $artist,
            'albums' => $albums,
            'music' => $music
        ]);
    }
}