<?php
/**
 * Album controller.
 */

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Comment;
use App\Form\Type\AlbumType;
use App\Form\Type\CommentType;
use App\Service\AlbumService;
use App\Service\AlbumServiceInterface;
use App\Service\CommentService;
use App\Service\CommentServiceInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * Comment service.
     *
     * @var CommentService
     */
    private CommentService $commentService;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param AlbumService $albumService
     */
    public function __construct(AlbumServiceInterface $albumService, CommentServiceInterface $commentService, TranslatorInterface $translator)
    {
        $this->albumService = $albumService;
        $this->commentService = $commentService;
        $this->translator = $translator;
    }


    /**
     * Index action.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route(
        name: 'album_index',
        methods: 'GET',
    )]
    public function index(Request $request): Response
    {
        $filters = $this->getFilters($request);
        $pagination = $this->albumService->getPaginatedList(
            $request->query->getInt('page', 1), $filters
        );

        return $this->render(
            'album/index.html.twig',
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
    #[Route('/create', name: 'album_create', methods: 'GET|POST', )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        $album = new Album();
        $album->setAuthor($user);
        $form = $this->createForm(
            AlbumType::class,
            $album,
            ['action' => $this->generateUrl('album_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->albumService->save($album);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('album_index');
        }

        return $this->render('album/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Show action.
     *
     * @param Album $album
     * @param Request $request
     *
     * @return Response
     */
    #[Route(
        '/{slug}',
        name: 'album_show',
        methods: 'GET|POST|DELETE',
    )]
    public function show(Album $album, Request $request): Response
    {
        $pagination = $this->commentService->getPaginatedListByAlbum(
            $album,
            $request->query->getInt('page', 1)
        );

        $numberOfComments = 0;
        try {
            $numberOfComments = $this->commentService->countByAlbum($album);
        } catch (NoResultException|NonUniqueResultException $e) {
            echo $e;
        }

        /* Add comment */
        if ($this->getUser()) {
            $comment = new Comment();
            $user = $this->getUser();
            $comment->setAuthor($user);
            $form = $this->createForm(
                CommentType::class,
                $comment,
                ['action' => $this->generateUrl('album_show', ['slug' => $album->getSlug()])]
            );
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($user->isBlocked()) {
                    $this->addFlash(
                        'warning',
                        $this->translator->trans('message.you_are_blocked_cant_comment')
                    );

                    return $this->redirectToRoute('album_show', ['slug' => $album->getSlug()]);
                }

                $comment->setAlbum($album);
                $this->commentService->save($comment);

                $this->addFlash(
                    'success',
                    $this->translator->trans('message.created_successfully')
                );

                return $this->redirectToRoute('album_show', ['slug' => $album->getSlug()]);
            }

            return $this->render(
                'album/show.html.twig',
                [
                    'album' => $album,
                    'pagination' => $pagination,
                    'number_of_comments' => $numberOfComments,
                    'form' => $form->createView(),
                ]
            );
        }

        return $this->render(
            'album/show.html.twig',
            [
                'album' => $album,
                'pagination' => $pagination,
                'number_of_comments' => $numberOfComments,
            ]
        );
    }


    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Album   $album   Album entity
     *
     * @return Response HTTP response
     */
    #[Route('/{slug}/edit', name: 'album_edit', methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'album')]
    public function edit(Request $request, Album $album): Response
    {
        $form = $this->createForm(
            AlbumType::class,
            $album,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('album_edit', ['slug' => $album->getSlug()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->albumService->save($album);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('album_index');
        }

        return $this->render(
            'album/edit.html.twig',
            [
                'form' => $form->createView(),
                'album' => $album,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Album   $album   Album entity
     *
     * @return Response HTTP response
     */
    #[Route('/{slug}/delete', name: 'album_delete', methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'album')]
    public function delete(Request $request, Album $album): Response
    {
        $form = $this->createForm(
            FormType::class,
            $album,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('album_delete', ['slug' => $album->getSlug()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->albumService->delete($album);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('album_index');
        }

        return $this->render(
            'album/delete.html.twig',
            [
                'form' => $form->createView(),
                'album' => $album,
            ]
        );
    }

    /**
     * Get filters from request.
     *
     * @param Request $request HTTP request
     *
     * @return array<string, int> Array of filters
     */
    private function getFilters(Request $request): array
    {
        $filters = [];
        $filters['tag_slug'] = $request->query->get('filters_tag_slug');

        return $filters;
    }

}
