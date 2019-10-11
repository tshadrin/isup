<?php
declare(strict_types=1);

namespace App\Form\UTM5;

use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ HiddenType, SubmitType, TextType };;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PassportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('userId', HiddenType::class,);
        $builder->add('number', TextType::class, [ 'required' => false, 'help' => 'passport_form.help.number', 'attr' => ['placeholder' => 'passport_form.placeholder.number',]]);
        $builder->add('issued', TextType::class, [ 'required' => false, 'help' => 'passport_form.help.issued', 'attr' => ['placeholder' => 'passport_form.placeholder.issued']]);
        $builder->add('authorityCode', TextType::class, [ 'required' => false, 'help' => 'passport_form.help.authorityCode',  'attr' => ['placeholder' => 'passport_form.placeholder.authorityCode']]);
        $builder->add('registrationAddress', TextType::class, [ 'required' => false, 'help' => 'passport_form.help.issued', 'attr' => ['placeholder' => 'passport_form.placeholder.registrationAddress']]);
        $builder->add('birthday', TextType::class, [ 'required' => false,  'help' => 'passport_form.help.birthday', 'attr' => ['placeholder' => 'passport_form.placeholder.birthday']]);
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PassportFormData::class,
        ]);
    }
}
