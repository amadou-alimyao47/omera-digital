<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your mail']
            ])
            ->add('fullname', TextType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your fullname']
            ])
            ->add('password', PasswordType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your password']
            ])
            ->add('passwordRepeat', PasswordType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Repeat your password']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
