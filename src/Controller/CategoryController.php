<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Repository\AlbumRepository;
use App\Service\CategoryService;
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
     * Category service.
     */
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Index action.
     */
    #[Route(
        name: 'category_index',
        methods: 'GET',
    )]
    public function index(Request $request): Response
    {
        $pagination = $this->categoryService->getPaginatedList(
            $request->query->getInt('page', 1)
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
        $pagination = $this->categoryService->getPaginatedListByCategory($category,
            $request->query->getInt('page',
                1));

        return $this->render(
            'category/show.html.twig',
            ['category' => $category, 'pagination' => $pagination]
        );
    }
}
