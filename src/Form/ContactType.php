<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType as TypeEmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Ce champ doit comporter au moins 3 caractères.'
                    ])
                ]
            ])
            ->add('email', TypeEmailType::class, [
                'label' => 'Votre email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Votre email est trop long.'
                    ])
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le sujet est trop long.'
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire.'
                    ]),
                    new Length([
                        'min' => 15,
                        'minMessage' => 'Vous n\'avez vraiment que ça à dire ?'
                    ])
                ]
            ])
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
