<?php
/**
 * Comment controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use App\Service\AlbumServiceInterface;
use App\Service\CommentService;
use App\Service\CommentServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController.
 */
#[Route('/comments')]
class CommentController extends AbstractController
{
    /**
     * Comment service.
     */
    private CommentService $commentService;

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

    public function __construct(CommentServiceInterface $commentService, TranslatorInterface $translator, AlbumServiceInterface $albumService)
    {
        $this->commentService = $commentService;
        $this->translator = $translator;
        $this->albumService = $albumService;
    }


    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/',
        name: 'comment_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    #[IsGranted('EDIT', subject: 'comment')]
    public function edit(Request $request, Comment $comment): Response
    {
        $user = $this->getUser();
        if ($user->isBlocked()) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.you_are_blocked_cant_edit_comment')
            );

            return $this->redirectToRoute('album_show', ['slug' => $comment->getAlbum()->getSlug()]);
        }

        $form = $this->createForm(
            CommentType::class,
            $comment,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('comment_edit', ['id' => $comment->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->save($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('album_show', ['slug' => $comment->getAlbum()->getSlug()]);
        }

        return $this->render(
            'comment/edit.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
                'album' => $comment->getAlbum(),
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Comment $comment Comment entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'comment_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE'
    )]
    #[IsGranted('DELETE', subject: 'comment')]
    public function delete(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(FormType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentAlbum = $comment->getAlbum();
            $this->commentService->delete($comment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('album_show', ['slug' => $commentAlbum->getSlug()]);
        }

        return $this->render(
            'comment/delete.html.twig',
            [
                'form' => $form->createView(),
                'comment' => $comment,
                'album' => $comment->getAlbum(),
            ]
        );
    }
}
