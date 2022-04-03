<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

final class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $builder->getData();

        $builder
            ->add('email', EmailType::class, [
                'label' => 'user.email',
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'user.first_name',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'user.last_name',
                'required' => false,
            ])
        ;

        if (null === $user->getId()) {
            $builder
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'label' => 'user.agree_terms',
                    'constraints' => [
                        new IsTrue([
                            'message' => 'user.agree_terms_error',
                        ]),
                    ],
                    'row_attr' => [
                        'class' => 'mt-2',
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'label' => 'user.password',
                    'type' => PasswordType::class,
                    'invalid_message' => 'user.password.mismatch',
                    'constraints' => [
                        new NotBlank(allowNull: false, message: 'user.password.not_blank'),
                    ],
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options' => [
                        'label' => 'user.password',
                        'attr' => ['autocomplete' => 'new-password'],
                    ],
                    'second_options' => [
                        'label' => 'user.password_repeat',
                        'attr' => ['autocomplete' => 'new-password'],
                    ],
                ])
            ;
        } else {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'mapped' => false,
                    'label' => 'user.current_password',
                    'required' => true,
                    'constraints' => [
                        new NotBlank(allowNull: false, message: 'user.current_password.not_blank'),
                        new UserPassword(message: 'user.current_password.invalid'),
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'label' => 'user.current_password',
                    'type' => PasswordType::class,
                    'invalid_message' => 'user.password.mismatch',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => false,
                    'first_options' => [
                        'label' => 'user.new_password',
                        'attr' => ['autocomplete' => 'new-password'],
                    ],
                    'second_options' => [
                        'label' => 'user.new_password_repeat',
                        'attr' => ['autocomplete' => 'new-password'],
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
