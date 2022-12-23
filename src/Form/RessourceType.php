<?php

namespace App\Form;

use App\Entity\Products;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class RessourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'mapped' => true
            ])
            ->add('price', IntegerType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'mapped' => true
            ])
            ->add('description', CKEditorType::class, [
                'mapped' => true,
                'required' => true,
                'attr' => ['class' => 'mb-3']
            ])
            ->add('pathImage', FileType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'mapped' => false,
                'label_attr' => ['class' => 'mt-3'],
                'label' => 'Image [278x548]',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Sorry but you can\'t assign an image who is more then 5M.',
                        'minWidth' => 548,
                        'minHeight' => 248,
                    ])
                ]
            ])
            ->add('pathLittleImage', FileType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'mapped' => false,
                'label_attr' => ['class' => 'mt-3'],
                'label' => 'Image [75x75]',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Sorry but you can\'t assign an image who is more then 2M.',
                        'maxWidth' => 150,
                        'maxHeight' => 150,
                        'minWidth' => 75,
                        'minHeight' => 75
                    ])
                ]
            ])
            ->add('ressource', FileType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'mapped' => false,
                'label_attr' => ['class' => 'mt-3'],
                'label' => 'Ressource',
                'required' => true,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'maxSizeMessage' => 'Sorry but you can\'t assign a file who is more then 10M.',
                        'mimeTypes' => [ "application/rar", "application/zip", "application/x-rar-compressed", "application/octet-stream","application/x-zip-compressed"],
                        'mimeTypesMessage' => "Please upload a valid rar/zip file!"
                    ])
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
