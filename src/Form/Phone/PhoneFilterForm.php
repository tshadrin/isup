<?php

namespace App\Form\Phone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PhoneFilterForm
 * @package App\Form\Phone
 */
class PhoneFilterForm extends AbstractType
{
    /**
     * Создание формы для поиска платежей
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('search', SearchType::class, [
            'label' => 'phone_filter_form.search_data',
            'required' => true,
            'attr' => [
                'placeholder' => 'phone_filter_form.search_placeholder',
                'class' => 'form-control form-control-sm m-1',
            ],
            'label_attr' => [
                'class' => 'col-auto col-form-label col-form-label-sm text-light m-1 pr-1',
            ],
        ]);
        $builder->add('submit_button', SubmitType::class, [
            'label' => 'phone_filter_form.search_button',
            'attr' => [
                'class' => 'btn btn-primary btn-sm btn-primary-sham m-1',
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phone_bundle_phone_filter_form';
    }
}
