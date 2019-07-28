<?php
declare(strict_types=1);

namespace App\Form\Vlan;

use App\Entity\Vlan\Vlan;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ TextType, SubmitType, IntegerType, CollectionType };
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма редактирования/добавления влана
 * Class VlanForm
 * @package App\Form\Vlan
 */
class VlanForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('number',
            IntegerType::class,
            [
                'label' => 'vlan_form.label.number',
                'attr' => [
                    'placeholder' => 'vlan_form.placeholder.number',
                    'class' => 'form-control',
                ],
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label font-weight-bold',
                ],
            ])
            ->add('name',
                TextType::class,
                [
                    'label' => 'vlan_form.label.name',
                    'attr' => [
                        'placeholder' => 'vlan_form.placeholder.name',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('points',
                CollectionType::class,
                [
                    'label' => 'vlan_form.label.points',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                    'entry_options'  => [
                        'attr' => [
                            'placeholder' => 'vlan_form.placeholder.points',
                            'class' => 'form-control',
                        ],
                    ],
                    'required' => false,
                ])
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'vlan_form.label.save',
                    'attr' => [
                        'class' => 'btn btn-primary btn-primary-sham m-1',
                    ],
                ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vlan::class,
            'translation_domain' => 'vlan',
        ]);
    }
}
