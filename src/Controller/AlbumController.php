<?php
/**
 * Album controller.
 */

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Service\AlbumService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AlbumController.
 */
#[Route('/albums')]
class AlbumController extends AbstractController
{
    /**
     * Album service.
     *
     * @var AlbumService
     */
    private AlbumService $albumService;

    /**
     * @param AlbumService $albumService
     */
    public function __construct(AlbumService $albumService)
    {
        $this->albumService = $albumService;
    }


    /**
     * Index action.
     *
     * @param AlbumRepository $repository
     *
     * @return Response
     */
    #[Route(
        name: 'album_index',
        methods: 'GET',
    )]
    public function index(Request $request): Response
    {
        $pagination = $this->albumService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'album/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param Album $album
     *
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
