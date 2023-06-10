<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Service\AlbumServiceInterface;
use App\Service\CategoryService;
use App\Service\CategoryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * Translator interface.
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * Album service.
     *
     * @var AlbumServiceInterface
     */
    private AlbumServiceInterface $albumService;

    public function __construct(CategoryServiceInterface $categoryService, TranslatorInterface $translator, AlbumServiceInterface $albumService)
    {
        $this->categoryService = $categoryService;
        $this->translator = $translator;
        $this->albumService = $albumService;
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
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'category_create',
        methods: 'GET|POST',
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Show action.
     */
    #[Route(
        '/{slug}',
        name: 'category_show',
        requirements: ['slug' => '[a-zA-Z\-]+'],
        methods: 'GET',
    )]
    public function show(Request $request, Category $category): Response
    {
        $pagination = $this->albumService->getPaginatedListByCategory($category,
            $request->query->getInt('page',
                1));

        return $this->render(
            'category/show.html.twig',
            ['category' => $category, 'pagination' => $pagination]
        );
    }


    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/edit',
        name: 'category_edit',
        requirements: ['slug' => '[a-zA-Z\-]+'],
        methods: 'GET|PUT'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(
            CategoryType::class,
            $category,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('category_edit', ['slug' => $category->getSlug()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/delete',
        name: 'category_delete',
        requirements: ['slug' => '[a-zA-Z\-]+'],
        methods: 'GET|DELETE'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Category $category): Response
    {
        if (!$this->categoryService->canBeDeleted($category)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.category_contains_albums')
            );

            return $this->redirectToRoute('category_show', ['slug' => $category->getSlug()]);
        }

        $form = $this->createForm(FormType::class, $category, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('category_delete', ['slug' => $category->getSlug()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
