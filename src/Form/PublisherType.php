<?php

namespace App\Form;

use App\Entity\Publisher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublisherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Device' => 1,
                    'Sensor' => 2,
                ],
                'attr' => ['class' => 'form-modal-input']
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-modal-input'],
                'label' => ''
            ])
            ->add('responseType', ChoiceType::class, [
                'choices' => [
                    'Integer' => 'int',
                    'Float' => 'float',
                    'Boolean' => 'bool',
                ],
                'attr' => ['class' => 'form-modal-input'],
                'placeholder' => 'Choose an option'
            ])
            ->add('location', HiddenType::class, [
                'required' => true,
                'mapped' => false
            ])
            ->add('publisherDescriptions', CollectionType::class, [
                'entry_type' => PublisherDescriptionType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publisher::class,
        ]);
    }
}
