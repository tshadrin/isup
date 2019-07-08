<?php
declare(strict_types=1);

namespace App\Form\SberbankReport;

use Symfony\Component\Form\{ AbstractType, CallbackTransformer, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ NumberType, TextType, SubmitType };
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PaymentFilterForm
 * @package App\Form\SberbankReport
 */
class PaymentFilterForm extends AbstractType
{
    /**
     * Создание формы для поиска платежей
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('account_id', NumberType::class, [
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
        $builder->add('pay_num', NumberType::class, [
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
        $builder->add('reg_date_interval',TextType::Class, [
            'label' => 'sber_form.reg_date_interval',
            'required' => true,
            'attr' => [
                'pattern' => '\d{2}-\d{2}-\d{4} - \d{2}-\d{2}-\d{4}',
                //'class' => 'form-input-date-range',
                'class' => 'form-control form-control-sm m-1',
            ],
            'label_attr' => [
                'class' => 'col-auto col-form-label col-form-label-sm text-light m-1 pr-1',
            ],
        ]);
        // преобразуем строку с датами в массив объектов datetime
        $builder->get('reg_date_interval')->addModelTransformer(new CallbackTransformer(
            function($intervalAsArray) {
                if($intervalAsArray) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    return "{$intervalAsArray[0]->format('d-m-Y')} - {$intervalAsArray[1]->format('d-m-Y')}";
                }
                return false;
            },
            function($intervalAsString) {
                $interval = explode(' - ', $intervalAsString);
                    return [
                        \DateTime::createFromFormat("d-m-Y H:i:s", "{$interval[0]} 00:00:00"),
                        \DateTime::createFromFormat("d-m-Y H:i:s", "{$interval[1]} 00:00:00")
                    ];
            }
        ));
        $builder->add('submit_button', SubmitType::class, [
            'label' => 'sber_form.search_button',
            'attr' => [
                'class' => 'btn btn-primary btn-sm btn-primary-sham m-1',
            ],
        ]);
    }

    /**
     * Стандартные параметры формы
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => 'App\SberbankEntity\Payment',
            'translation_domain' => 'sber',
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'sberbank_report_bundle_payment_filter_form';
    }
}
