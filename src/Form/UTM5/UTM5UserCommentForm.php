<?php

namespace App\Form\UTM5;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UTM5UserCommentForm  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment',
            TextType::class,
            [
                'attr' => [
                    'placeholder' => 'utm5_user_comment.placeholder.comment',
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('utmId',
                HiddenType::class)
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'utm5_user_comment.save',
                    'attr' => [
                        'class' => 'btn btn-primary btn-sm btn-primary-sham',
                    ],
                ]
            );

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => '\App\Entity\UTM5\UTM5UserComment',
        ]);
    }
}
