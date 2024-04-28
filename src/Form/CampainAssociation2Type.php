<?php

namespace App\Form;

use App\Entity\Referent;
use App\Entity\CampainAssociation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{HiddenType, TextareaType, TextType, ChoiceType};

class CampainAssociation2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'association',
                AssoUpdate2Type::class,
                [
                    'label'         => false,
                ],
            )
            ->add('texte_personnalise', TextareaType::class, [
                'label' => 'Texte de présentation pour la campagne ',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5 // Ajustez le nombre de lignes en fonction de vos besoins
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampainAssociation::class,
            'include_fields' => [
                'association',
                'statut',
                'texte_personnalise',
                'emails',
                'adresse',
                'cp',
                'city',
                'tel'
            ],
        ]);
    }
}
