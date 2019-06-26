<?php

namespace App\Form\Order;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderFilterType  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET')
            ->add('utmId',
            TextType::class,
            [
                'label' => 'order.form.utm_id',
                'required' => false,
                'attr' => [
                    'placeholder' => 'order.placeholder.utm_id',
                    'class' => 'form-control form-control-sm',
                ],
                'label_attr' => [
                    'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                ],
            ])
            ->add('fullname',
                TextType::class,
                [
                    'label' => 'order.form.full_name',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.full_name',
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('address',
                TextType::class,
                [
                    'label' => 'order.form.address',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.address',
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('servername',
                TextType::class,
                [
                    'label' => 'order.form.server_name',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.server_name',
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('ipaddress',
                TextType::class,
                [
                    'label' => 'order.form.ip',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.ip',
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('comment',
                TextType::class,
                [
                    'label' => 'order.form.comment',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.comment',
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('mobileTelephone',
                TextType::class,
                [
                    'label' => 'order.form.phone',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'order.placeholder.phone',
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('executed',
                EntityType::class,
                [
                    'group_by' => 'region',
                    'label' => 'order.form.executed',
                    'class' => "App\Entity\User\User",
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.onWork = 1')
                            ->andWhere('u.id > 0')
                            ->orderBy('u.region', 'ASC')
                            ->orderBy('u.fullName', 'ASC');
                    },
                    'attr' => [
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('status',
                EntityType::class,
                [
                    'label' => 'order.form.status',
                    'class' => "App\Entity\Intercom\Status",
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control form-control-sm',
                    ],
                    'label_attr' => [
                        'class' => 'col-xs-2 col-lg-2 col-form-label-sm font-weight-bold',
                    ],
                ])
            ->add('search',
                SubmitType::class,
                [
                    'label' => 'order.form.saveandback',
                    'attr' => [
                        'class' => 'btn btn-primary btn-sm btn-primary-sham m-1',
                    ],
                ]
            );
    }
}