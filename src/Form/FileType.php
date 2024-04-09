<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;
use Symfony\Component\Form\Extension\Core\Type\FileType as FormFileType;

class FileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FormFileType::class, [
                'label' => '<i class="fas fa-file-excel"></i> Fichier XLSX',
                'label_html' => true, // Permet d'interpréter le HTML dans le label

                'mapped' => false,
                'required' => false,

                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un fichier.',
                    ]),
                    new ConstraintsFile([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier au format XLSX.',
                    ]),
                ],

            ])
            ->add('name', null, [
                'required' => true,
            ]);
    }
}
