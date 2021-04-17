<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Contributor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('authors', EntityType::class, [
              'class' => Contributor::class,
              'required' => true,
              'choice_label' => 'fullname',
              'multiple' => true,
            ])
            ->add('categories', EntityType::class, [
              'class' => Category::class,
              'required' => true,
              'choice_label' => 'name',
              'multiple' => true,
            ])
            ->add('pictures', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
