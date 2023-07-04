<?php
/**
 * Change password type.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Change password type class.
 */
class ChangePasswordType extends AbstractType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder Builder
     * @param array                $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'required' => true,
                'label' => 'label.old_password',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'label' => 'label.new_password',

                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'min-length' => 6, 'max_length' => 4096],
                    'label' => 'label.new_password',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'label.password_repeat',
                ],
                'mapped' => true,
            ])
        ;
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
