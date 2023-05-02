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
}
