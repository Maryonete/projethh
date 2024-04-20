<?php

namespace App\Form;

use App\Entity\Referent;
use App\Entity\Association;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReferentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tel', TextType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label'         =>  'Téléphone',
                'label_attr'    =>  [
                    'class'     =>  'col-form-label mt-2'
                ],
            ])

            ->add('user', UserType::class, [
                'label' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Referent::class,
        ]);
    }
}
