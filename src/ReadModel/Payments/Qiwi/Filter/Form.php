<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\Qiwi\Filter;


use App\ReadModel\DateIntervalTransformer;
use App\ReadModel\Payments\Qiwi\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType,  SearchType, TextType };
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('userId', SearchType::class, [
            'label' => 'filter_form.label.user_id',
            'required' => false,
            'attr' => [
                'placeholder' => 'filter_form.placeholder.user_id',
            ],
        ]);
        $builder->add('command', ChoiceType::class, [
            'choices'  => [
                'filter_form.choice.check' => Payment::COMMAND_CHECK,
                'filter_form.choice.pay' => Payment::COMMAND_PAY,
            ],
            'label' => 'filter_form.label.command',
            'required' => false,
        ]);
        $builder->add('processed', ChoiceType::class, [
            'choices'  => [
                'filter_form.choice.processed' => Payment::STATUS_PROCESSED,
                'filter_form.choice.not_processed' => Payment::STATUS_NOT_PROCESSED,
            ],
            'label' => 'filter_form.label.processed',
            'required' => false,

        ]);
        $builder->add('fisk', ChoiceType::class, [
            'choices'  => [
                'filter_form.choice.fiscal' => Payment::STATUS_FISCAL,
                'filter_form.choice.not_fiscal' => Payment::STATUS_NOT_FISCAL,
            ],
            'label' => 'filter_form.label.fiscal',
            'required' => false,
        ]);
        $builder->add('interval',TextType::Class, [
            'label' => 'filter_form.label.interval',
            'required' => false,
            'attr' => [
                'pattern' => '\d{2}-\d{2}-\d{4} - \d{2}-\d{2}-\d{4}',
            ],
        ]);
        $builder->get('interval')->addModelTransformer(DateIntervalTransformer::factory());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'translation_domain' => 'qiwi',
        ]);
    }
}