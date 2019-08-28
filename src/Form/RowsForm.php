<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType };
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для ввода количества записей на странице
 * Class RowsForm
 * @package App\Form
 */
class RowsForm extends AbstractType
{
    const TWENTY_ROWS_ON_PAGE = 20;
    const THIRTY_ROWS_ON_PAGE = 30;
    const FIFTY_ROWS_ON_PAGE = 50;
    const ONE_HUNDRED_ROWS_ON_PAGE = 100;
    const TWO_HUNDRED_ROWS_ON_PAGE = 200;
    const THREE_HUNDRED_ROWS_ON_PAGE = 300;
    const FIVE_HUNDRED_ROWS_ON_PAGE = 500;
    public const ALL_ROWS_ON_PAGE = 0;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('value', ChoiceType::class, [
                'label' => 'rows_form.label_value',
                'choices'  => [
                        'rows_form.20' => self::TWENTY_ROWS_ON_PAGE,
                        'rows_form.30' => self::THIRTY_ROWS_ON_PAGE,
                        'rows_form.50' => self::FIFTY_ROWS_ON_PAGE,
                        'rows_form.100' => self::ONE_HUNDRED_ROWS_ON_PAGE,
                        'rows_form.200' => self::TWO_HUNDRED_ROWS_ON_PAGE,
                        'rows_form.300' => self::THREE_HUNDRED_ROWS_ON_PAGE,
                        'rows_form.500' => self::FIVE_HUNDRED_ROWS_ON_PAGE,
                        'rows_form.all' => self::ALL_ROWS_ON_PAGE,
                ],
                'attr' => [
                    'onChange' => 'this.form.submit()',
                ],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rows::class,
            'method' => 'POST',
            'csrf_protection' => false,
            'translation_domain' => 'phone',
        ]);
    }
}
