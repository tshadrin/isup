<?php
declare(strict_types=1);

namespace App\Form\Phone;

use App\Entity\Phone\Phone;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ TextareaType, TextType, SubmitType };
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('number',
            TextType::class,
            [
                'label' => 'phone_form.label.number',
                'attr' => [
                    'placeholder' => 'phone_form.placeholder.number',
                ],
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label font-weight-bold',
                ],
            ])
            ->add('moscownumber',
                TextType::class,
                [
                    'label' => 'phone_form.label.moscow_number',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.moscow_number',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('location',
                TextType::class,
                [
                    'label' => 'phone_form.label.location',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.location',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('name',
                TextType::class,
                [
                    'label' => 'phone_form.label.name',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.name',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('contactnumber',
                TextType::class,
                [
                    'label' => 'phone_form.label.contact_number',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.contact_number',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('ip',
                TextType::class,
                [
                    'label' => 'phone_form.label.ip',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.ip',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('login',
                TextType::class,
                [
                    'label' => 'phone_form.label.login',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.login',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('password',
                TextType::class,
                [
                    'label' => 'phone_form.label.password',
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.password',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('notes',
                TextareaType::class,
                [
                    'label' => 'phone_form.label.notes',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'phone_form.placeholder.notes',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'phone_form.label.save',
                    'attr' => [
                        'class' => 'btn btn-primary btn-primary-sham m-1',
                    ],
                ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Phone::class,
            'translation_domain' => 'phone',
        ]);
    }
}
