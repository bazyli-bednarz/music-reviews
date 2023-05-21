<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Repository\AlbumRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController.
 */
#[Route('/categories')]
class CategoryController extends AbstractController
{
    /**
     * Index action.
     *
     * @param PaginatorInterface $paginato
     */
    #[Route(
        name: 'category_index',
        methods: 'GET',
    )]
    public function index(Request $request, CategoryRepository $repository, PaginatorInterface $paginator): Response
    {
        //        $categories = $repository->findAll();
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1),
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     */
    #[Route(
        '/{id}',
        name: 'category_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(Request $request, Category $category, PaginatorInterface $paginator, AlbumRepository $repository): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryByCategory($category),
            $request->query->getInt('page', 1),
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'category/show.html.twig',
            ['category' => $category, 'pagination' => $pagination]
        );
    }
}
