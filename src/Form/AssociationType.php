<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Association;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{TextType, EmailType, TextareaType};


class AssociationType extends AbstractType
{
    const FORM_LABEL_CLASS = 'col-form-label mt-2';
    public function __construct(private UserRepository $userRepository)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $currentAssociationId = $options['current_association_id'];

        if ($options["data"]) {
            $currentAssociation = $options["data"];
        }
        $builder
            ->add('code', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '1',
                    'maxlength' => '10',
                ],
                'label'         =>  'Code asso',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
            ])
            ->add('libelle', TextType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '250',
                ],
                'label'         =>  'Libellé',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
                'constraints'   => [
                    new Assert\Length(['min' => 2, 'max' => 250]),
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir le libellé de l\'association'
                    ])
                ]
            ])
            ->add('adress', TextareaType::class, [
                'attr'  => [
                    'class'     =>  'form-control',
                    'rows' => 5
                ],
                'label'         =>  'Adresse',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
                'required' => false,

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
                'attr'  => [
                    'class'     =>  'form-control',
                    'minlength' => '2',
                    'maxlength' => '12',
                ],
                'label'         =>  'Téléphone',
                'label_attr'    =>  [
                    'class'     =>  self::FORM_LABEL_CLASS
                ],
                'required'  => false,
                'empty_data' => '',
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
            ])
            ->add('president', EntityType::class, [
                'class'         => User::class,
                'choices'       => $this->userRepository->findAllUserNonPresident($currentAssociationId),
                'choice_label'  =>  function ($user) {
                    return $user->__toString();
                },
                'data' => ($currentAssociation && $currentAssociation->getPresident()) ?
                    $currentAssociation->getPresident()->getUser() : null,
                'placeholder'   => 'Sélectionner un(e) président(e)',
                'attr'          => ['class' => 'form-select'],
                'required'      => false,
                'mapped' => false,
            ])
            ->add('referent', EntityType::class, [
                'class'         => User::class,
                'choices'       => $this->userRepository->findAllUserNonReferent($currentAssociationId),
                'choice_label'  =>  function ($user) {
                    return $user->__toString();
                },
                'data' => ($currentAssociation && $currentAssociation->getReferent()) ?
                    $currentAssociation->getReferent()->getUser() : null,
                'placeholder'   => 'Sélectionner un(e) référent(e)',
                'attr'          => ['class' => 'form-select'],
                'required'      => false,
                'mapped'        => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'                => Association::class,
            'current_association_id'    => null,
        ]);
    }
}
