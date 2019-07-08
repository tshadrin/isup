<?php
declare(strict_types=1);

namespace App\Form\UTM5;

use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ HiddenType, TextType, SubmitType };
use Symfony\Component\OptionsResolver\OptionsResolver;

class UTM5UserCommentForm  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => '\App\Entity\UTM5\UTM5UserComment',
        ]);
    }
}
