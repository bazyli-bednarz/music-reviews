<?php
/**
 * Album controller.
 */

namespace App\Controller;

use App\Entity\Album;
use App\Form\Type\AlbumType;
use App\Service\AlbumService;
use App\Service\AlbumServiceInterface;
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
     * Translator.
     *
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @param AlbumService $albumService
     */
    public function __construct(AlbumServiceInterface $albumService, TranslatorInterface $translator)
    {
        $this->albumService = $albumService;
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

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'album_create', methods: 'GET|POST', )]
    public function create(Request $request): Response
    {
        $album = new Album();
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
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Album   $album   Album entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'album_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Album $album): Response
    {
        $form = $this->createForm(
            AlbumType::class,
            $album,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('album_edit', ['id' => $album->getId()]),
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
    #[Route('/{id}/delete', name: 'album_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Album $album): Response
    {
        $form = $this->createForm(
            FormType::class,
            $album,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('album_delete', ['id' => $album->getId()]),
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

}
