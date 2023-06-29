<?php
/**
 * Registration form type.
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

/**
 * Class RegistrationFormType.
 */
class RegistrationFormType extends AbstractType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'label.user_email',
                    'required' => true,
                    'attr' => ['max_length' => 150],
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'label.user_name',
                    'required' => true,
                    'attr' => ['max_length' => 150],
                ]
            )
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [new IsTrue()],
                    'label' => 'label.agree_terms',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'mapped' => true,
                    'required' => true,
                    'label' => 'label.user_password',
                    'attr' => ['autocomplete' => 'new-password', 'min-length' => 6, 'max_length' => 4096],
                ]
            );
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity(
                    [
                    'entityClass' => User::class,
                    'fields' => 'email',
                    ]
                ),
            ],
        ]);
    }
}
