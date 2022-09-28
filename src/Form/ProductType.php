<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Color;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit comporter au moins 3 caractères.',
                        'max' => 255,
                        'maxMessage' => 'Le nom ne peut excéder 255 caractères.'
                    ])
                ]
            ])
            ->add('description', null, [
                'label' => 'Description',
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'La description doit comporter au moins 10 caractères.'
                    ])
                ]
            ])
            ->add('price', null, [
                'label' => 'Prix (en centimes)',
                'attr' => [
                    'min' => 9900,
                    'max' => 100000
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ]),
                    new GreaterThan([
                        'value' => 9900,
                        'message' => 'Le prix ne peut pas être inférieur à 99€.'
                    ]),
                    new LessThan([
                        'value' => 100000,
                        'message' => 'Le prix ne peut pas excéder 1000€'
                    ])
                ]
            ])
            ->add('favorite', null, [
                'label' => 'Favori ?'
            ])
            ->add('image', UrlType::class, [
                'label' => 'URL de l\'image',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'L\'URL ne peut excéder 255 caractères.'
                    ])
                ]
            ])
            ->add('promotion', null, [
                'label' => 'Promotion (en %)',
                'attr' => [
                    'min' => 0,
                    'max' => 100
                ],
                'constraints' => [
                    new PositiveOrZero([
                        'message' => 'La promotion ne peut pas être négative.'
                    ]),
                    new LessThan([
                        'value' => 100,
                        'message' => 'La promotion être égale ou excéder 100%.'
                    ])
                ]
            ])
            ->add('colors', EntityType::class, [
                'class' => Color::class,
                'mapped' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ])
                ]
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
