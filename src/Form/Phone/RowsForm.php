<?php

namespace App\Form\Phone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Форма для ввода количества
 * записей на странице
 * Class RowsForm
 * @package App\Form\Phone
 */
class RowsForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'rows',
            ChoiceType::class,
            [
                'label' => 'form_rows.rows_on_page',
                'choices'  =>
                    [
                        20 => 20,
                        50 => 50,
                        100 => 100,
                        200 => 200,
                        300 => 300,
                        500 => 500,
                        'all' => 'all',
                    ],
            ]
        )->add(
            'apply',
            SubmitType::class,
            [
                'label' => 'form_rows.apply',
            ]
        );
    }
}
