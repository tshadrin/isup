<?php
declare(strict_types=1);

namespace App\Form\UTM5;

use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use App\Entity\UTM5\TypicalCall;
use App\Repository\UTM5\TypicalCallRepository;
use Symfony\Component\Form\Extension\Core\Type\{ HiddenType, TextType, SubmitType };
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypicalCallForm  extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('call_type',
            EntityType::class,
            [
                'class' => TypicalCall::class,
                'query_builder' => function (TypicalCallRepository $er) {
                    return $er->createQueryBuilder('tc')
                        ->where('tc.enabled = 1');
                },
                'attr' => [
                    'placeholder' => 'utm5_user_comment.placeholder.comment',
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('utm5_id',
                HiddenType::class)
            ->add('add',
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
        //$resolver->setDefaults([
       //     'data_class' => '\App\Entity\UTM5\UTM5UserComment',
        //]);
    }
}
