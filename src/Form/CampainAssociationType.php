<?php

namespace App\Form;

use App\Entity\CampainAssociation;
use App\Entity\Referent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\{TextareaType, TextType, ChoiceType};

class CampainAssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('association', AssoUpdateType::class, [
                'label' => false
            ])

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
            'include_fields' => ['association', 'statut', 'texte_personnalise', 'emails', 'adresse', 'cp', 'city', 'tel'],
        ]);
    }
}
