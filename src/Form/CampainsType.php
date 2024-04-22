<?php

namespace App\Form;

use App\Entity\Campains;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{TextType, DateType, ChoiceType, EmailType, TextareaType};

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
                'label' => 'Date du début de la campagne', // Libellé du champ
                'label_attr' => [
                    'class' => 'col-form-label mt-2', // Classe CSS pour le libellé
                ],
                'constraints' => [
                    // Ajoutez ici vos contraintes de validation si nécessaire
                ],
            ])

            ->add('objet_email', TextType::class, [
                'label' => 'Objet de l\'email',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('texte_email', TextareaType::class, [
                'label' => 'Texte de l\'email par défaut',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5 // Ajustez le nombre de lignes en fonction de vos besoins
                ]
            ])
            ->add('destinataire', ChoiceType::class, [
                'choices' => [
                    'Président(e)s' => 'presidents',
                    'Référent(e)s'  => 'referents',
                ],
                'expanded' => true, // Afficher les choix sous forme de cases à cocher
                'multiple' => true, // Permettre la sélection de plusieurs destinataires
                'required' => true, // Le champ n'est pas obligatoire
                'label' => 'Destinataires', // Libellé du champ
                'choice_attr' => [
                    'Président(e)s' => ['class' => 'mx-2'],
                    'Référent(e)s' => ['class' => 'mx-2'],
                ],
            ])
            ->add('email_from', EmailType::class, [
                'label' => 'Email de l\'expéditeur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email_cc', EmailType::class, [
                'label' => 'Email en copie',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('oldcampain', EntityType::class, [
                'class' => Campains::class,
                'label' => 'Ancienne campagne',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Campains::class,
        ]);
    }
}
