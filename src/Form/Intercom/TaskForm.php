<?php

namespace App\Form\Intercom;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;

class TaskForm  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone',
            TextType::class,
            [
                'label' => 'task.phone',
                'attr' => [
                    'placeholder' => 'task.placeholder.phone',
                    'pattern' => '^8\(\d{3}\)\d{3}\-\d{2}\-\d{2}$',
                    'class' => 'form-control form-control-lg',
                ],
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label-lg font-weight-bold',
                ],
            ])
            ->add('fullname',
                TextType::class,
                [
                    'label' => 'task.fullname',
                    'attr' => [
                        'placeholder' => 'task.placeholder.fullname',
                        'class' => 'form-control form-control-lg',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label-lg font-weight-bold',
                    ],
                ])
            ->add('address',
                TextType::class,
                [
                    'label' => 'task.address',
                    'attr' => [
                        'placeholder' => 'task.placeholder.address',
                        'class' => 'form-control form-control-lg',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label-lg font-weight-bold',
                    ],
                ])
            ->add('description',
                TextareaType::class,
                [
                    'label' => 'task.description',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'task.placeholder.description',
                        'class' => 'form-control form-control-lg',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label-lg font-weight-bold',
                    ],
                    'constraints' => new Length(['min' => 3]),
                ])
            ->add('status',
                EntityType::class,
                [
                    'label' => 'task.status',
                    'class' => "App\Entity\Intercom\Status",
                    'attr' => [
                        'class' => 'form-control form-control-lg',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label-lg font-weight-bold',
                    ],
                ])
            ->add('type',
                EntityType::class,
                [
                    'label' => 'task.type',
                    'class' => "App\Entity\Intercom\Type",
                    'attr' => [
                        'class' => 'form-control form-control-lg',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label-lg font-weight-bold',
                    ],
                ])
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'task.save',
                    'attr' => [
                        'class' => 'btn btn-primary btn-lg btn-primary-sham m-1',
                    ],
                ])
            ->add('saveandlist',
                SubmitType::class,
                [
                    'label' => 'task.saveandlist',
                    'attr' => [
                        'class' => 'btn btn-primary btn-lg btn-primary-sham m-1',
                    ],
                ])

            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\App\Entity\Intercom\Task',
        ]);
    }
}
