<?php
/**
 * Album controller.
 */

namespace App\Controller;


use App\Entity\Album;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AlbumController.
 */
#[Route('/albums')]
class AlbumController extends AbstractController
{

    /**
     * Index action.
     *
     * @param AlbumRepository $repository
     *
     * @return Response
     */
    #[Route(
        name: 'record_index',
        methods: 'GET',
    )]
    public function index(AlbumRepository $repository): Response
    {
        $albums = $repository->findAll();

        return $this->render(
            'album/index.html.twig',
            ['albums' => $albums]
        );
    }

    /**
     * Show action.
     *
     * @param Album $album
     * @return Response
     */
    #[Route(
        '/{id}',
        name: 'album_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(Album $album): Response
    {
        return $this->render(
            'album/show.html.twig',
            ['album' => $album]
        );
    }
}
