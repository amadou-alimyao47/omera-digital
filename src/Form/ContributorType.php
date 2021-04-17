<?php

namespace App\Form;

use App\Entity\Contributor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContributorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullname', TextType::class)
            ->add('email', EmailType::class)
            ->add('profession', TextType::class)
            ->add('photoImage', FileType::class, [
              'label' => 'Une photo de profile',
              'multiple' => false,
              'required' => false,
              'mapped' => false,
              'constraints' => [
                 new File([
                  'maxSize' => '5000k',
                  'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/jpg'
                  ],
                  'mimeTypesMessage' => 'Veuillez envoyer une image au format png, jpg, jpeg, de 4MB  maximum'
                  ])
              ],
              'attr' => [
                'accept' => '.jpg, .jpeg, .png'
              ]
            ])
            ->add('isMember', CheckboxType::class, [
              'label' => ' Est membre d\'Omera',
              'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contributor::class,
        ]);
    }
}
