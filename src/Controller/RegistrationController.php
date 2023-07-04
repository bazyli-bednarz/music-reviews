<?php
/**
 * Registration controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Registration controller.
 */
class RegistrationController extends AbstractController
{
    /**
     * Translator.
     *
     * @var TranslatorInterface Translator
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Register action.
     *
     * @param Request                     $request            Request
     * @param UserPasswordHasherInterface $userPasswordHasher Hasher
     * @param UserAuthenticatorInterface  $userAuthenticator  Authenticator
     * @param LoginFormAuthenticator      $authenticator      Authenticator
     * @param EntityManagerInterface      $entityManager      Entity manager
     *
     * @return Response Response
     */
    #[Route(
        '/register',
        name: 'app_register',
    )]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.registered_successfully'));

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
