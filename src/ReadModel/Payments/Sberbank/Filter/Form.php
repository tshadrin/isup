<?php
declare(strict_types=1);

namespace App\ReadModel\Payments\Sberbank\Filter;

use DateTimeImmutable;
use Symfony\Component\Form\{ AbstractType, CallbackTransformer, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ NumberType, TextType };
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PaymentFilterForm
 * @package App\Form\SberbankReport
 */
class Form extends AbstractType
{
    /**
     * Создание формы для поиска платежей
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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

        $builder->get('interval')->addModelTransformer(new CallbackTransformer(
            static function(?array $interval): ?string
            {
                if (!is_null($interval)) {
                    return "{$interval[0]->format('d-m-Y')} - {$interval[1]->format('d-m-Y')}";
                }
                return null;
            },
            static function(?string $interval): ?array
            {
                if (!is_null($interval)) {
                    [$from, $to] = explode(' - ', $interval);
                    return [
                        DateTimeImmutable::createFromFormat("!d-m-Y", $from),
                        DateTimeImmutable::createFromFormat("!d-m-Y", $to)
                    ];
                }
                return null;
            }
        ));
    }

    /**
     * Стандартные параметры формы
     * @param OptionsResolver $resolver
     */
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
