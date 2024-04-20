<?php

namespace App\Form;

use App\Entity\CampainAssociation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextareaType, TextType, ChoiceType};

class CampainAssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet_email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '10',
                ],
                'label'         =>  'Objet de l\'Email',
                'label_attr'    =>  [
                    'class'     =>  'col-form-label mt-2'
                ],
            ])
            ->add('texte_email', TextareaType::class, [

                'attr' => [
                    'rows' => 20,
                    'placeholder' => 'Texte de l\'email'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampainAssociation::class,
        ]);
    }
}
