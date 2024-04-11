<?php

namespace App\Form;

use App\Entity\UserApi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserApiEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'mapped' => true,
                'attr' => ['class' => 'form-modal-input'],
                'label' => ''
            ])
            ->add('old_password', PasswordType::class, [
                'attr' => ['class' => 'form-modal-input'],
                'label' => '',
                'mapped' => false,
                'required' => false
            ])
            ->add('new_password', PasswordType::class, [
                'attr' => ['class' => 'form-modal-input'],
                'label' => '',
                'mapped' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserApi::class,
        ]);
    }
}
