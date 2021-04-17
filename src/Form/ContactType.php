<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', TextType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your Name']
            ])
            ->add('email', EmailType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your email']
            ])
            ->add('subject', TextType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your Subject']
            ])
            ->add('budget', NumberType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Your Budget']
            ])
            ->add('challenge', TextareaType::class, [
              'label' => false,
              'attr' => ['placeholder' => 'Briefly tell us about your project.']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
