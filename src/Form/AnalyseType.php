<?php

namespace App\Form;

use App\Entity\Analyse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AnalyseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class,[
                'label' => 'Image(jpg,png,jpeg)',
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                // unmapped means that this field is not associated to any entity property
                'mapped' => true,
                'required' => true,
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
            ->add('nomImage',TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' =>'2',
                    'maxlength' => '50'
                ],
                'label' => 'Nom Image (*)',
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label'
                ]
            ])
            ->add('fiability', IntegerType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max'=> 100
                ],
                'label' => 'Fiabilité en % (*)',
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label'
                ]
            ])
            ->add('resultat', ChoiceType::class,[
                'choices'  => [
                    'Saine' => 'Saine',
                    'Malade' => 'Malade'
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Résultat (*)',
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => '7'
                ],
                'required' => false,
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Ajouter Image'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Analyse::class,
        ]);
    }
}
