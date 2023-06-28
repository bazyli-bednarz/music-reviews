<?php
///**
// * Cover controller.
// */
//
//namespace App\Controller;
//
//use App\Entity\Cover;
//use App\Entity\Album;
//use App\Form\Type\CoverType;
//use App\Service\CoverServiceInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\File\UploadedFile;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Contracts\Translation\TranslatorInterface;
//
///**
// * Class CoverController.
// */
//#[Route('/{slug}/cover')]
//class CoverController extends AbstractController
//{
//    /**
//     * Cover service.
//     */
//    private CoverServiceInterface $coverService;
//
//    /**
//     * Translator.
//     */
//    private TranslatorInterface $translator;
//
//    /**
//     * Constructor.
//     *
//     * @param CoverServiceInterface $coverService Cover service
//     * @param TranslatorInterface    $translator    Translator
//     */
//    public function __construct(CoverServiceInterface $coverService, TranslatorInterface $translator)
//    {
//        $this->coverService = $coverService;
//        $this->translator = $translator;
//    }
//
//    /**
//     * Create action.
//     *
//     * @param Request $request HTTP request
//     *
//     * @return Response HTTP response
//     */
//    #[Route(
//        '/create',
//        name: 'cover_create',
//        methods: 'GET|POST'
//    )]
//    public function create(Request $request): Response
//    {
////        /** @var Album $album */
////        $album = $this->getAlbum();
////        if ($album->getCover()) {
////            return $this->redirectToRoute(
////                'cover_edit',
////                ['id' => $album->getId()]
////            );
////        }
//
//        $cover = new Cover();
//        $form = $this->createForm(
//            CoverType::class,
//            $cover,
//            ['action' => $this->generateUrl('cover_create')]
//        );
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var UploadedFile $file */
//            $file = $form->get('file')->getData();
//            $this->coverService->create(
//                $file,
//                $cover,
//                $album
//            );
//
//            $this->addFlash(
//                'success',
//                $this->translator->trans('message.created_successfully')
//            );
//
//            return $this->redirectToRoute('task_index');
//        }
//
//        return $this->render(
//            'cover/create.html.twig',
//            ['form' => $form->createView()]
//        );
//    }
//
//}
