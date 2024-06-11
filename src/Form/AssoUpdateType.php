<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{TextType,  EmailType, TextareaType};

class AssoUpdateType extends AbstractType
{
    const FORM_LABEL_CLASS = 'col-form-label mt-2';
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('president', PresidentType::class, [
                'label' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                dump($event->getData());
                /* @var Association $association */
                $association = $event->getData();
                $form = $event->getForm();

                if (null !== $association->getReferent()) {
                    $form->add('referent', ReferentType::class, [
                        'label'     => false,
                        'required'  => false,
                    ]);
                }
            })
            ->add('adress', TextareaType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'rows' => 5,
                ],
                'label'         =>  'Adresse',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
                'constraints'   => [
                    new Assert\Length(['min' => 2, 'max' => 250]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir l\'adresse'
                    ])
                ]
            ])
            ->add('cp', TextType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '5',
                    'maxlength' => '10',
                ],
                'label'         =>  'Code postal',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
                'constraints'   => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir le code postal'
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label'         =>  'Ville',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
                'constraints'   => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir la ville'
                    ])
                ]
            ])
            ->add('tel', TextType::class, [
                'required'  =>  false,
                // 'empty_data' => '',
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label'         =>  'Téléphone',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
            ])
            ->add('email', EmailType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label'         =>  'E-mail',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
                'constraints'   => [
                    new Assert\Length(['min' => 2, 'max' => 80]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir l\'adresse email'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
