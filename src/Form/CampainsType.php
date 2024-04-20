<?php

namespace App\Form;

use App\Entity\Campains;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;

class CampainsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label'         =>  'Libellé',
                'label_attr'    =>  [
                    'class'     =>  'col-form-label mt-2'
                ],
                'constraints'   => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir le numéro de téléphone'
                    ])
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                // Ajoutez d'autres options selon vos besoins
                'attr' => [
                    'class' => 'form-control', // Classe CSS pour le champ de date
                ],
                'label' => 'Date', // Libellé du champ
                'label_attr' => [
                    'class' => 'col-form-label mt-2', // Classe CSS pour le libellé
                ],
                'constraints' => [
                    // Ajoutez ici vos contraintes de validation si nécessaire
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campains::class,
        ]);
    }
}
