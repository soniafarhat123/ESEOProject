<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName',TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' =>'2',
                    'maxlength' => '50'
                ],
                'label' => 'Nom / Prénom (*)',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('pseudo',TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' =>'2',
                    'maxlength' => '50'
                ],
                'required' => false,
                'label' => 'Pseudo',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('image', FileType::class,[
                'label' => 'Image(jpg,png,jpeg)',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                // unmapped means that this field is not associated to any entity property
                'mapped' => true,
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Image invalide : (jpg,png,jpeg)'
                    ])
                ],

            ])
            ->add('email',EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' =>'2',
                    'maxlength' => '180'
                ],
                'label' => 'Adresse Email (*)',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('password',RepeatedType::class, [
                'type' => PasswordType::class,

                'first_options' => [
                    'label' => 'Mot de passe (*)',
                    'label_attr' => [
                        'class' => 'form-label'
                    ],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ],
                'second_options' =>[
                    'label' => 'Confirmation du mot de passe (*)',
                    'label_attr' => [
                        'class' => 'form-label'
                    ],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary d-grid w-100 mt-4'
                ],
                'label' => 'S\'inscrire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
