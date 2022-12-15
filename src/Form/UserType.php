<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'required' => false,
                'mapped' => false
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'required' => false,
                'mapped' => false
            ])
            ->add('age', IntegerType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'required' => false,
                'mapped' => false
            ])
            ->add('pseudo', TextType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'required' => false,
                'mapped' => false
            ])
            ->add('pathImage', FileType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'data_class' => null,
                'required' => false,
                'mapped' => false,
            ])
            ->add('path_banner', FileType::class, [
                'attr' => ['class' => 'form-control mb-3'],
                'data_class' => null,
                'mapped' => false,
                'required' => false
            ])
            ->add('description', CKEditorType::class, [
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Length([
                        'min' => 0,
                        'max' => 500,
                        'maxMessage' => 'Your description cannot be longer than {{ limit }} characters.'])
                ]
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
