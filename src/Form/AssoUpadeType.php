<?php
# TODO https://yoandev.co/symfony-ux-autocomplete
namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{TextType,  EmailType, TextareaType};

class AssoUpdateType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referent', ReferentType::class, [
                'label' => false
            ])
            ->add('president', PresidentType::class, [
                'label' => false,
            ])

            ->add('adress', TextareaType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'rows' => 5,
                ],
                'label'         =>  'Adresse',
                'label_attr'    =>  [
                    'class'     =>  'col-form-label mt-2'
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
                    'class'     =>  'col-form-label mt-2'
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
                    'class'     =>  'col-form-label mt-2'
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
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '50',
                ],
                'label'         =>  'Téléphone',
                'label_attr'    =>  [
                    'class'     =>  'col-form-label mt-2'
                ],
                'constraints'   => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir le numéro de téléphone'
                    ])
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
                    'class'     =>  'col-form-label mt-2'
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
