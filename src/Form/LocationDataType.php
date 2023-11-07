<?php

namespace App\Form;

use App\Entity\IconImage;
use App\Entity\Location;
use App\Entity\UserApi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LocationDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-modal-input'],
                'label' => ''
            ])
            ->add('id', HiddenType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('icon', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-modal-input'],
                'constraints' => [
                    new File([
                        'maxSize' => '512k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/svg+xml'
                        ]
                    ])
                ],
            ])
            ->add('icon_image', EntityType::class, [
                'class' => IconImage::class,
                'required' => false,
                'attr' => ['class' => 'form-modal-input'],
                'choice_label' => 'title'
            ])
            ->add('user_api', EntityType::class, [
                'required' => true,
                'mapped' => true,
                'class' => UserApi::class,
                'attr' => ['class' => 'form-modal-input'],
                'choices' => $options['choices'],
                'placeholder' => 'Choose an option'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
            'csrf_protection' => false,
            'choices' => []
        ]);
        $resolver->setAllowedTypes('choices', 'array');
    }
}
