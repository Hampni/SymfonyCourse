<?php

namespace App\Controller;


use App\Entity\Artists;
use App\Entity\Songs;
use App\Repository\AlbumsRepository;
use App\Repository\ArtistsRepository;
use App\Repository\SongsRepository;
use Doctrine\Persistence\ManagerRegistry;
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
                                  SongsRepository $songsRepository, ManagerRegistry $managerRegistry): Response
    {


        $artist = $artistsRepository->find($id);
        $albums = $albumsRepository->findBy(['authorId' => $artistsRepository->find($id)]);
        $music = [];
        foreach ($albums as $index => $album) {
        $music[$album->getId()] = $songsRepository->findBy(['albumId' => $album->getId()]);
        };

        return $this->render('artistDetails.html.twig', [
            'artist' => $artist,
            'albums' => $albums,
            'music' => $music
        ]);


    }
}