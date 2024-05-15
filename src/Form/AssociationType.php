<?php
# TODO https://yoandev.co/symfony-ux-autocomplete
namespace App\Form;

use App\Entity\User;
use App\Entity\Referent;
use App\Entity\President;
use App\Entity\Association;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\{TextType, EmailType, TextareaType};

class AssociationType extends AbstractType
{
    public function __construct(private UserRepository $userRepository)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentAssociationId = $options['current_association_id'];
        $builder
            ->add('code', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '10',
                ],
                'label'         =>  'Code asso',
                'label_attr'    =>  [
                    'class'     =>  'col-form-label mt-2'
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
                    'class'     =>  'col-form-label mt-2'
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
                    'class'     =>  'col-form-label mt-2'
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
                'required'  => false,
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
            ])
            ->add('president', EntityType::class, [
                'class'     => User::class,
                'choices'   => $this->userRepository->findAllUserNonPresident($currentAssociationId),
                'choice_label' => function ($president) {
                    return $president;
                },
                'placeholder' => 'Sélectionner un(e) président(e)',
                'attr' => ['class' => 'form-select'],
                'required' => false,
            ])
            ->add('referent', EntityType::class, [
                'class'         => Referent::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $currentAssociationId = $options['current_association_id'];
                    if ($currentAssociationId) {
                        // Utilisez $currentAssociationId dans votre query_builder
                        return $er->createQueryBuilder('r')

                            ->leftJoin('r.user', 'u') // Jointure avec la table User associée
                            // Jointure avec la table Association associée
                            ->where('r.association IS NULL OR r.association = :currentAssociationId')
                            ->setParameter('currentAssociationId', $currentAssociationId)
                            ->orderBy('u.lastname', 'ASC') // Tri par nom de famille de l'utilisateur
                            ->addOrderBy('u.firstname', 'ASC');
                    } else {
                        // Si $currentAssociationId est vide, retournez simplement les référents sans filtrage
                        return $er->createQueryBuilder('r')
                            ->leftJoin('r.user', 'u')
                            ->where('r.association IS NULL ')
                            ->orderBy('u.lastname', 'ASC') // Tri par nom de famille de l'utilisateur
                            ->addOrderBy('u.firstname', 'ASC'); // Puis tri par prénom du référent
                    }
                },

                'choice_label' => function ($referent) {
                    return $referent;
                },
                'attr'          => ['class' => 'form-select'],
                'placeholder' => 'Sélectionner un(e) référent(e)',
                'required' => false,

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
            'current_association_id' => null,
        ]);
    }
}
