<?php

namespace App\Form;

use App\Entity\Analyse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnalyseEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fiability',IntegerType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max'=> 100
                ],
                'label' => 'Fiabilité en % (*)',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ])
            ->add('description',TextareaType::class, [
        'attr' => [
            'class' => 'form-control',
            'rows' => '9'
        ],
        'required' => false,
        'label' => 'Description',
        'label_attr' => [
            'class' => 'form-label'
        ]
    ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary right',
                ],

                'label' => 'Modifier'
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
