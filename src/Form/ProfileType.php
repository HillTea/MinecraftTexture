<?php

namespace App\Form;

use App\Entity\User;
use Faker\Core\File;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('age', IntegerType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control mb-3'],
                'label' => 'Age'

            ])
            ->add('pathImage', FileType::class, [
                'invalid_message' => "You can't do that!",
                'attr' => ['class' => 'form-control mb-3'],
                'label' => 'Avatar',
                'mapped' => false,
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'maxSize' => '200k',
                        'maxSizeMessage' => 'Sorry but you can\'t assign an image who is more then 5M.'
                    ])
                ]
            ])
            ->add('path_banner', FileType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'mapped' => false,
                'label' => 'Banner',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'maxSize' => '200k',
                        'maxSizeMessage' => 'Sorry but you can\'t assign an image who is more then 5M.'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'label_attr' => ['class' => 'mt-3'],
                'label' => 'Description'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
