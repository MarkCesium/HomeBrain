<?php

namespace App\Form;

use App\Entity\PublisherDescription;
use App\Entity\PublisherSetting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublisherDescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', options: [
                'attr' => ['class' => 'form-modal-input'],
            ])
            ->add('publisherSetting', EntityType::class, [
                'class' => PublisherSetting::class,
                'attr' => ['class' => 'form-modal-input '],
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PublisherDescription::class,
        ]);
    }
}
