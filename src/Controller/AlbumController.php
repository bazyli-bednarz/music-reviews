<?php
/**
 * Album controller.
 */

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
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
    public function index(Request $request, AlbumRepository $repository, PaginatorInterface $paginator): Response
    {
        $albums = $repository->findAll();
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            AlbumRepository::PAGINATOR_ITEMS_PER_PAGE
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
