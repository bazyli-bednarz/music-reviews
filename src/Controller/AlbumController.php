<?php
/**
 * Album controller.
 */

namespace App\Controller;


use App\Repository\AlbumRepository\AlbumRepository;
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
     * @param AlbumRepository $repository
     * @param int $id
     *
     * @return Response
     */
    #[Route(
        '/{id}',
        name: 'album_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(AlbumRepository $repository, int $id): Response
    {
        $album = $repository->findOneById($id);

        return $this->render(
            'album/show.html.twig',
            ['album' => $album]
        );
    }
}
