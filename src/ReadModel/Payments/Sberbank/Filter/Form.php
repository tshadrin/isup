<?php
declare(strict_types=1);

namespace App\ReadModel\Payments\Sberbank\Filter;

use App\ReadModel\DateIntervalTransformer;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ NumberType, TextType };
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('userId', NumberType::class, [
            'label' => 'sber_form.account_id',
            'required' => false,
            'attr' => [
                'placeholder' => 'sber_form.id_placeholder',
                'pattern' => '\d*',
                'class' => 'form-control form-control-sm m-1',
            ],
            'label_attr' => [
                'class' => 'col-auto col-form-label col-form-label-sm text-light m-1 pr-1',
            ],
        ]);

        $builder->add('transaction', NumberType::class, [
            'label' => 'sber_form.pay_num',
            'required' => false,
            'attr' => [
                'placeholder' => 'sber_form.transaction_placeholder',
                'pattern' => '\d*',
                'class' => 'form-control form-control-sm m-1',
            ],
            'label_attr' => [
                'class' => 'col-auto col-form-label col-form-label-sm text-light m-1 pr-1',
            ],
        ]);

        $builder->add('interval',TextType::Class, [
            'label' => 'sber_form.reg_date_interval',
            'required' => false,
            'attr' => [
                'pattern' => '\d{2}-\d{2}-\d{4} - \d{2}-\d{2}-\d{4}',
                'class' => 'form-control form-control-sm m-1',
            ],
            'label_attr' => [
                'class' => 'col-auto col-form-label col-form-label-sm text-light m-1 pr-1',
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
            'translation_domain' => 'sber',
        ]);
    }
}
