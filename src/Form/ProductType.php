<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('createdAt')
            ->add('favorite')
            ->add('image')
            ->add('promotion')
            ->add('colors', EntityType::class, [
                'class' => Color::class,
                'mapped' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'mapped' => false
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
