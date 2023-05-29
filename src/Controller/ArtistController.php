<?php
/**
 * Artist controller.
 */

namespace App\Controller;

use App\Entity\Artist;
use App\Form\Type\ArtistType;
use App\Service\AlbumServiceInterface;
use App\Service\ArtistService;
use App\Service\ArtistServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ArtistController.
 */
#[Route('/artists')]
class ArtistController extends AbstractController
{
    /**
     * Artist service.
     */
    private ArtistService $artistService;

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

    public function __construct(ArtistServiceInterface $artistService, TranslatorInterface $translator, AlbumServiceInterface $albumService)
    {
        $this->artistService = $artistService;
        $this->translator = $translator;
        $this->albumService = $albumService;
    }

    /**
     * Index action.
     */
    #[Route(
        name: 'artist_index',
        methods: 'GET',
    )]
    public function index(Request $request): Response
    {
        $pagination = $this->artistService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'artist/index.html.twig',
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
        name: 'artist_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->artistService->save($artist);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('artist_index');
        }

        return $this->render(
            'artist/create.html.twig',
            ['form' => $form->createView()]
        );
    }


    /**
     * Show action.
     */
    #[Route(
        '/{slug}',
        name: 'artist_show',
        requirements: ['slug' => '[a-zA-Z\-]+'],
        methods: 'GET',
    )]
    public function show(Request $request, Artist $artist): Response
    {
        $pagination = $this->albumService->getPaginatedListByArtist($artist,
            $request->query->getInt('page',
                1));

        return $this->render(
            'artist/show.html.twig',
            ['artist' => $artist, 'pagination' => $pagination]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Artist  $artist  Artist entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/edit',
        name: 'artist_edit',
        requirements: ['slug' => '[a-zA-Z\-]+'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Artist $artist): Response
    {
        $form = $this->createForm(
            ArtistType::class,
            $artist,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('artist_edit', ['slug' => $artist->getSlug()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->artistService->save($artist);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('artist_index');
        }

        return $this->render(
            'artist/edit.html.twig',
            [
                'form' => $form->createView(),
                'artist' => $artist,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Artist  $artist  Artist entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/delete',
        name: 'artist_delete',
        requirements: ['slug' => '[a-zA-Z\-]+'],
        methods: 'GET|DELETE'
    )]
    public function delete(Request $request, Artist $artist): Response
    {
        if (!$this->artistService->canBeDeleted($artist)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.artist_contains_albums')
            );

            return $this->redirectToRoute('artist_show', ['slug' => $artist->getSlug()]);
        }

        $form = $this->createForm(FormType::class, $artist, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('artist_delete', ['slug' => $artist->getSlug()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->artistService->delete($artist);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('artist_index');
        }

        return $this->render(
            'artist/delete.html.twig',
            [
                'form' => $form->createView(),
                'artist' => $artist,
            ]
        );
    }
}
