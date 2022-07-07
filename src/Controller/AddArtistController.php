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


class AddArtistController extends AbstractController
{

    /**
     * @Route("/admin/artists")
     */
    public function admin(ArtistsRepository $artistsRepository, AlbumsRepository $albumsRepository, SongsRepository $songsRepository,
                          Request $request, SluggerInterface $slugger, ManagerRegistry $managerRegistry): Response
    {


        // Artist adding
        $artist = new Artists();
        $formCreateArtist = $this->createFormBuilder($artist)
            ->add('name', TextType::class)
            ->add('imageArtist', FileType::class, [
                    'label' => 'Загрузите фото исполнителя',
                    'mapped' => false
                ]
            )
            ->add('save', SubmitType::class)
            ->getForm();


        //Request artist handling
        $formCreateArtist->handleRequest($request);
        if ($formCreateArtist->isSubmitted() && $formCreateArtist->isValid()) {
            $requestData = $formCreateArtist->getData();

            // File uploading handling
            $file = $request->files->get('form')['imageArtist'];
            $artistsImagesDirectory = $this->getParameter('artists_images_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $artistsImagesDirectory,
                $filename
            );

            $name = $requestData->getName();
            $count = 0;

            $artist->setName('' . $name);
            $artist->setImage('' . $filename);
            $artist->setCount('' . $count);

            $artistAdd = new ArtistsRepository($managerRegistry);
            $artistAdd->add($artist, true);
        }

        //Find artist request handling
        $variants = ['Мияги', 'Мия'];

        if (empty($_POST['nameToFind'])) {
            $allArtists = $this->artists($artistsRepository);
        }  elseif (in_array($_POST['nameToFind'], $variants)) {
            $allArtists = $artistsRepository->findBy(['name' => 'Miyagi']) ;
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

        return $this->render('addArtist.html.twig', [
            'addArtistForm' => $formCreateArtist->createView(),
            'artists' => $allArtists,
            'postRequest' => $postRequest,
            'sorry' => $sorry
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

}