<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', TextType::class, [
              'label' => 'Nom du client',
              'attr' => ['placeholder' => 'nom du client']
            ])
            ->add('email', EmailType::class, [
              'label' => 'Adresse mail du client',
              'attr' => ['placeholder' => 'address mail client']
            ])
            ->add('subject', TextType::class, [
              'label' => 'Sujet du projet',
              'attr' => ['placeholder' => 'subject']
            ])
            ->add('budget', NumberType::class, [
              'label' => 'Budget du client',
              'attr' => ['placeholder' => 'Budget']
            ])
            ->add('challenge', TextareaType::class, [
              'label' => 'Challenge',
              'attr' => ['placeholder' => 'challenge']
            ])
            ->add('solution', TextareaType::class, [
              'label' => "Solution",
              'attr' => ['placeholder' => 'solution']
            ])
            ->add('status', ChoiceType::class, [
              'label' => "Etat du projet",
              'choices' => [
                'dev' => 'dev',
                'prod' => 'prod'
              ]
            ])
            ->add('satisfiedCustomer', CheckboxType::class, [
              'label' => " Client statisfait ",
              'required' => false
            ])
            ->add('categories', EntityType::class, [
              'class' => Category::class,
              'required' => true,
              'choice_label' => 'name',
              'multiple' => true,
              'label' => 'Categorie du projet'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
