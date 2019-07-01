<?php

namespace App\Form\UTM5;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PassportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('userId', HiddenType::class);
        $builder->add('number', TextType::class, [ 'required' => false,]);
        $builder->add('issued', TextType::class, [ 'required' => false,]);
        $builder->add('authorityCode', TextType::class, [ 'required' => false,]);
        $builder->add('registrationAddress', TextType::class, [ 'required' => false, ]);
        $builder->add('birthday', TextType::class, [ 'required' => false, ]);
        $builder->add('save',
        SubmitType::class,
        [
            'label' => 'task.save',
            'attr' => [
                'class' => 'btn btn-primary btn-primary-sham m-1',
            ],
        ])
        ->add('saveandback',
            SubmitType::class,
            [
                'label' => 'Save and show user info',
                'attr' => [
                    'class' => 'btn btn-primary btn-primary-sham m-1',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PassportFormData::class,
        ]);
    }
}
