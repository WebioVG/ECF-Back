<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', null, [
                'label' => 'Note',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La note est obligatoire.'
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'La note ne peut pas être inférieure à 1.'
                    ]),
                    new LessThanOrEqual([
                        'value' => 5,
                        'message' => 'La note ne peut pas être supérieure à 5.'
                    ])
                ],
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Message',
                'constraints' => [
                    new NotBlank([
                        'message' => 'La note est obligatoire.'
                    ]),
                    new Length([
                        'max' => 1500,
                        'maxMessage' => 'Votre message ne peut pas excéder 1500 caractères.'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
