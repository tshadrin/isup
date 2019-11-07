<?php
declare(strict_types=1);

namespace App\Form\Order;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ TextareaType, TextType, SubmitType };
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderForm  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('utmId',
            TextType::class,
            [
                'label' => 'order.form.utm_id',
                'required' => false,
                'attr' => [
                    'placeholder' => 'order.placeholder.utm_id',
                    'class' => 'form-control ',
                ],
                'label_attr' => [
                    'class' => 'col-sm-2 col-form-label font-weight-bold',
                ],
            ])
            ->add('fullname',
                TextType::class,
                [
                    'label' => 'order.form.full_name',
                    'attr' => [
                        'placeholder' => 'order.placeholder.full_name',
                        'class' => 'form-control ',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('address',
                TextType::class,
                [
                    'label' => 'order.form.address',
                    'attr' => [
                        'placeholder' => 'order.placeholder.address',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('servername',
                TextType::class,
                [
                    'label' => 'order.form.server_name',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.server_name',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('ip',
                TextType::class,
                [
                    'label' => 'order.form.ip',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.ip',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('comment',
                TextareaType::class,
                [
                    'label' => 'order.form.comment',
                    'attr' => [
                        'placeholder' => 'order.placeholder.comment',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('phone',
                TextType::class,
                [
                    'label' => 'order.form.phone',
                    'attr' => [
                        'placeholder' => 'order.placeholder.phone',
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('executed',
                EntityType::class,
                [
                    'group_by' => 'region',
                    'label' => 'order.form.executed',
                    'class' => "App\Entity\User\User",
                    'required' => true,
                    'placeholder' => 'not set',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.onWork = 1')
                            ->andWhere('u.id > 0')
                            ->orderBy('u.region', 'ASC')
                            ->orderBy('u.fullName', 'ASC');
                    },
                    'data' => '',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('status',
                EntityType::class,
                [
                    'label' => 'order.form.status',
                    'class' => "App\Entity\Intercom\Status",
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-2 col-form-label font-weight-bold',
                    ],
                ])
            ->add('saveandback',
                SubmitType::class,
                [
                    'label' => 'order.form.saveandback',
                    'attr' => [
                        'class' => 'btn btn-primary btn-primary-sham m-1',
                    ],
                ]
            )
            ->add('saveandlist',
                SubmitType::class,
                [
                    'label' => 'order.form.saveandlist',
                    'attr' => [
                        'class' => 'btn btn-primary btn-primary-sham m-1',
                    ],
                ]
            );

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => '\App\Entity\Order\Order',
        ]);
    }
}
