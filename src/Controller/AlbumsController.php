<?php

namespace App\Controller;


use App\Entity\Artists;
use App\Entity\Songs;
use App\Repository\AlbumsRepository;
use App\Repository\ArtistsRepository;
use App\Repository\SongsRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
                                  SongsRepository $songsRepository, ManagerRegistry $managerRegistry, Security $security, UserRepository $userRepository): Response
    {

        $artist = $artistsRepository->find($id);
        $albums = $albumsRepository->findBy(['authorId' => $artistsRepository->find($id)]);
        $music = [];
        $songIds = [];
        foreach ($albums as $index => $album) {
            $music[$album->getId()] = $songsRepository->findBy(['albumId' => $album->getId()]);
        };

        //adding liked song
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if (!empty($_POST['liked'])) {
                $user = $userRepository->find($this->getUser()->getId());
                $user->addSong($songsRepository->find($_POST['liked']));
                $songAdd = new UserRepository($managerRegistry);
                $songAdd->add($user, true);
            }
        }

        //liked songs handling
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            //user
            $user = $userRepository->find($this->getUser()->getId());

            //get array of songs (objects)
            $songsArray = $user->getSongs()->getValues();

            //get array of id of the songs
            foreach ($songsArray as $song) {
                $songIds[] = $song->getId();
            }
        }


        return $this->render('artistDetails.html.twig', [
            'artist' => $artist,
            'albums' => $albums,
            'music' => $music,
            'likedSongs' => $songIds
        ]);
    }


}