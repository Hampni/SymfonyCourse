<?php

namespace App\Controller;

use App\Entity\Albums;
use App\Entity\Artists;
use App\Entity\Songs;
use App\Repository\AlbumsRepository;
use App\Repository\ArtistsRepository;
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


class AddAlbumController extends AbstractController
{

    /**
     * @Route("/admin/albums")
     */
    public function admin(ArtistsRepository $artistsRepository, AlbumsRepository $albumsRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        //Albums adding
        $album = new Albums();
        $allArtists = $artistsRepository->findBy([]);
        $allArtistsId = [];
        foreach ($allArtists as $artist) {
            $allArtistsId[$artist->getName()] = $artist->getId();
        }

        $formNewAlbum = $this->createFormBuilder($album)
            ->add('author_id',ChoiceType::class, [
                'label' => 'Исполнитель',
                'choices' => $allArtistsId
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

            $author_id = $requestData->getId();
            $name = $requestData->getName();
            $count = 0;

            $album->setName('' . $name);
            $album->setImage('' . $filename);
            $album->setCountAlbums('' . $count);

            $albumAdd = new AlbumsRepository($managerRegistry);
            $albumAdd->add($album, true);
        }


        return $this->render('addAlbum.html.twig', [
            'newAlbumForm' => $formNewAlbum->createView()
        ]);

    }

}