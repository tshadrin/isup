<?php

namespace App\Form\Phone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Форма редактирования/добавления телефона
 * Class PhoneForm
 * @package App\Form\Phone
 */
class PhoneForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('number',
            TextType::class,
            [
                'label' => 'phone.number',
                'attr' => [
                    'placeholder' => 'phone_form.placeholder.number',
                ],
            ])
            ->add('moscownumber',
                TextType::class,
                [
                    'label' => 'phone.moscow_number',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.moscow_number',
                    ],
                ])
            ->add('location',
                TextType::class,
                [
                    'label' => 'phone.location',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.location',
                    ],
                ])
            ->add('name',
                TextType::class,
                [
                    'label' => 'phone.name',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.name',
                    ],
                ])
            ->add('contactnumber',
                TextType::class,
                [
                    'label' => 'phone.contact_number',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.contact_number',
                    ],
                ])
            ->add('ip',
                TextType::class,
                [
                    'label' => 'phone.ip',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.ip',
                    ],
                ])
            ->add('login',
                TextType::class,
                [
                    'label' => 'phone.login',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.login',
                    ],
                ])
            ->add('password',
                TextType::class,
                [
                    'label' => 'phone.password',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.password',
                    ],
                ])
            ->add('notes',
                TextareaType::class,
                [
                    'label' => 'phone.notes',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.notes',
                    ],
                ])
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'phone_form.save',
                ])
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\App\Entity\Phone\Phone',
            //'translation_domain' => 'messages',
        ]);
    }
}
