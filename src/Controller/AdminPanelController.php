<?php

namespace App\Controller;

use App\Entity\Albums;
use App\Entity\Artists;
use App\Entity\Songs;
use App\Repository\AlbumsRepository;
use App\Repository\ArtistsRepository;
use App\Repository\SongsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AdminPanelController extends AbstractController
{

    /**
     * @Route("/admin")
     */
    public function admin(): Response
    {

        return $this->render('adminPanel.html.twig', [
        ]);

    }

    /**
     * @Route("/admin/albums")
     */
    public function album(ArtistsRepository $artistsRepository, AlbumsRepository $albumsRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        //Albums adding
        $album = new Albums();
        $allArtists = $artistsRepository->findBy([]);
        $allArtistsId = [];
        foreach ($allArtists as $artist) {
            $allArtistsId[$artist->getName()] = $artist->getId();
        }

        $formNewAlbum = $this->createFormBuilder($album)
            ->add('name', TextType::class, [
                'label' => 'Название альбома'
            ])
            ->add('imageAlbum', FileType::class, [
                    'label' => 'Загрузите фото нового альбома',
                    'mapped' => false
                ]
            )
            ->add('save', SubmitType::class)
            ->getForm();


        //Request artist handling
        $formNewAlbum->handleRequest($request);
        if ($formNewAlbum->isSubmitted() && $formNewAlbum->isValid()) {
            $requestData = $formNewAlbum->getData();

            // File uploading handling
            $file = $request->files->get('form')['imageAlbum'];
            $albumsImagesDirectory = $this->getParameter('albums_images_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $albumsImagesDirectory,
                $filename
            );

            $author_id = $requestData->getId();
            $name = $requestData->getName();
            $count = 0;

            $album->setName('' . $name);
            $album->setImage('' . $filename);
            $album->setCountAlbums('' . $count);

            $albumAdd = new AlbumsRepository($managerRegistry);
            $albumAdd->add($album, true);
        }

        $variants = ['Мияги', 'Мия'];

        if (empty($_POST['nameToFind'])) {
            $allArtists = $this->artists($artistsRepository);
        } elseif (in_array($_POST['nameToFind'], $variants)) {
            $allArtists = $artistsRepository->findBy(['name' => 'Miyagi']);
        } else {
            $allArtists = $this->artistsRequest($artistsRepository);
        }

        // for keeping searching field full
        if (!empty($_POST['nameToFind'])) {
            $postRequest = $_POST['nameToFind'];
        } else {
            $postRequest = '';
        }

        // if nothing found
        if (!empty($_POST['nameToFind']) && empty($allArtists)) {
            $sorry = 'Ничего не нашлось. Попробуйте изменить запрос';
        } else {
            $sorry = '';
        }


        return $this->render('addAlbum.html.twig', [
            'newAlbumForm' => $formNewAlbum->createView(),
            'artists' => $allArtists,
            'postRequest' => $postRequest,
            'sorry' => $sorry
        ]);

    }

    /**
     * @Route("/admin/albums/{id}")
     */
    public function adminAlbum($id, ArtistsRepository $artistsRepository, SongsRepository $songsRepository, AlbumsRepository $albumsRepository, Request $request, ManagerRegistry $managerRegistry)
    {


        //Albums adding
        $album = new Albums();
        $allArtists = $artistsRepository->findBy([]);
        $allArtistsId = [];
        if (!empty($_POST['artistAddId'])) {
            $neededId = $_POST['artistAddId'];
        } else {
            $neededId = '';
        }

        foreach ($allArtists as $artist) {
            $allArtistsId[$artist->getName()] = $artist->getId();
        }

        $formNewAlbum = $this->createFormBuilder($album)
            ->add('authorId', HiddenType::class, [
                'data' => $neededId
            ])
            ->add('name', TextType::class, [
                'label' => 'Название альбома'
            ])
            ->add('imageAlbum', FileType::class, [
                    'label' => 'Загрузите фото нового альбома',
                    'mapped' => false
                ]
            )
            ->add('save', SubmitType::class)
            ->getForm();


        //Request artist handling
        $formNewAlbum->handleRequest($request);
        if ($formNewAlbum->isSubmitted() && $formNewAlbum->isValid()) {
            $requestData = $formNewAlbum->getData();

            // File uploading handling
            $file = $request->files->get('form')['imageAlbum'];
            $albumsImagesDirectory = $this->getParameter('albums_images_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $albumsImagesDirectory,
                $filename
            );

            $author_id = $requestData->getAuthorId();
            $name = $requestData->getName();
            $count = 0;

            $album->setAuthorId('' . $author_id);
            $album->setName('' . $name);
            $album->setImage('' . $filename);
            $album->setCountAlbums('' . $count);

            $albumAdd = new AlbumsRepository($managerRegistry);
            $albumAdd->add($album, true);
        }

        $artist1 = $artistsRepository->find($id);
        $albums1 = $albumsRepository->findBy(['authorId' => $artistsRepository->find($id)]);
        $music1 = [];
        foreach ($albums1 as $index => $album) {
            $music1[$album->getId()] = $songsRepository->findBy(['albumId' => $album->getId()]);
        };


        return $this->render('albumsOfArtist.html.twig', [
            'newAlbumForm' => $formNewAlbum->createView(),
            'artists' => $allArtists,
            'artist1' => $artist1,
            'albums1' => $albums1,
            'music1' => $music1
        ]);


    }

    // All artists displaying
    public function artists(ArtistsRepository $artistsRepository)
    {
        $artists = $artistsRepository->findBy([]);
        return $artists;
    }

    //Requested artists
    public function artistsRequest(ArtistsRepository $artistsRepository)
    {
        $artistsRequest = $artistsRepository->findBy(['name' => $_POST['nameToFind']]);
        return $artistsRequest;
    }


    /**
     * @Route("/admin/songs/{id}")
     */
    public function addSong($id, ArtistsRepository $artistsRepository, AlbumsRepository $albumsRepository, SongsRepository $songsRepository, Request $request, ManagerRegistry $managerRegistry)
    {


        $album = $albumsRepository->find($id);
        $artist = $artistsRepository->find($album->getAuthorId());

        $song = new Songs();
        $formNewSong = $this->createFormBuilder($song)
            ->add('name', TextType::class, [
                'label' => 'Название песни'
            ])
            ->add('audio', FileType::class, [
                    'label' => 'Загрузите аудио',
                    'mapped' => false
                ]
            )
            ->add('save', SubmitType::class)
            ->getForm();

        //Request songs handling
        $formNewSong->handleRequest($request);
        if ($formNewSong->isSubmitted() && $formNewSong->isValid()) {
            $requestData = $formNewSong->getData();

            // File uploading handling
            $file = $request->files->get('form')['audio'];
            $audiosDirectory = $this->getParameter('audio_directory');
            $audioname = $requestData->getName() . '.' . $file->guessExtension();
            $file->move(
                $audiosDirectory,
                $audioname
            );


            $album_id = $id;
            $author_id = $artist->getId();
            $name = $requestData->getName();
            $count = 0;

            $song->setAuthorId('' . $author_id);
            $song->setAlbumId(''. $album_id);
            $song->setName('' . $name);
            $song->setCountSongs('' . $count);

            $songAdd = new SongsRepository($managerRegistry);
            $songAdd->add($song, true);
        }

        $songs = $songsRepository->findBy(['albumId' => $album->getId()]);


        return $this->render('addSong.html.twig', [
            'formNewSong' => $formNewSong->createView(),
            'album' => $album,
            'songs' => $songs
        ]);
    }

}