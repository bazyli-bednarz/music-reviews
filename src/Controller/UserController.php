<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\BlockUserType;
use App\Form\Type\ChangePasswordType;
use App\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
#[Route('/users')]
class UserController extends AbstractController
{
    /**
     * User service.
     *
     * @var UserServiceInterface User service
     */
    private UserServiceInterface $userService;

    /**
     * Translator interface.
     *
     * @var TranslatorInterface Translator
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(UserServiceInterface $userService, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request Request
     *
     * @return Response Response
     */
    #[Route(
        name: 'user_index',
        methods: 'GET',
    )]
    public function index(Request $request): Response
    {
        $pagination = $this->userService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'user/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return Response Response
     */
    #[Route(
        '/{slug}',
        name: 'user_show',
        requirements: ['slug' => '[a-zA-Z0-9\-]+'],
        methods: 'GET',
    )]
    public function show(Request $request, User $user): Response
    {
        return $this->render(
            'user/show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * Change password action.
     *
     * @param Request                     $request            Request
     * @param UserPasswordHasherInterface $userPasswordHasher Hasher
     * @param TranslatorInterface         $translator         Translator
     * @param User                        $user               User
     *
     * @return Response Response
     */
    #[Route(
        '/{slug}/change-password',
        name: 'user_edit',
        requirements: ['slug' => '[a-zA-Z0-9\-]+'],
        methods: 'GET|POST'
    )]
    #[IsGranted('EDIT', 'user')]
    public function edit(Request $request, UserPasswordHasherInterface $userPasswordHasher, TranslatorInterface $translator, User $user): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            if (!$userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                $this->addFlash(
                    'danger',
                    $translator->trans('message.old_password_wrong')
                );

                return $this->redirectToRoute('user_index');
            }

            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );

            $this->userService->upgradePassword($user, $encodedPassword);

            $this->addFlash(
                'success',
                $translator->trans('message.password_changed')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/change-password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Block user action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/block',
        name: 'user_block',
        requirements: ['slug' => '[a-zA-Z0-9\-]+'],
        methods: 'GET|PUT'
    )]
    #[IsGranted('BLOCK', 'user')]
    public function block(Request $request, User $user): Response
    {
        $activeUser = $this->getUser();

        if (null !== $activeUser && $activeUser !== $user) {
            $form = $this->createForm(BlockUserType::class, $user, [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_block', ['slug' => $user->getSlug()]),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($user->isBlocked()) {
                    $user->setBlocked(false);
                    $this->addFlash(
                        'success',
                        $this->translator->trans('message.unblocked_user')
                    );
                } else {
                    $user->setBlocked(true);
                    $this->addFlash(
                        'success',
                        $this->translator->trans('message.blocked_user')
                    );
                }
                $this->userService->save($user);

                return $this->redirectToRoute('user_show', ['slug' => $user->getSlug()]);
            }

            return $this->render(
                'user/block.html.twig',
                [
                    'user' => $user,
                    'form' => $form->createView(),
                ]
            );
        } else {
            $this->addFlash(
                'danger',
                $this->translator->trans('message.cant_block_this_user')
            );
        }

        return $this->redirectToRoute('user_show', ['slug' => $user->getSlug()]);
    }
}
