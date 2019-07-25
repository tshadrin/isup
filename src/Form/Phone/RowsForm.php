<?php
declare(strict_types=1);

namespace App\Form\Phone;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType };
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для ввода количества записей на странице
 * Class RowsForm
 * @package App\Form\Phone
 */
class RowsForm extends AbstractType
{
    public const ALL_ROWS = "all";

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('rows', ChoiceType::class, [
                'label' => 'form_rows.rows_on_page',
                'choices'  => [
                        20 => 20,
                        50 => 50,
                        100 => 100,
                        200 => 200,
                        300 => 300,
                        500 => 500,
                        'all' => self::ALL_ROWS,
                ],
                'attr' => [
                    'onChange' => 'this.form.submit()',
                ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'csrf_protection' => false,
        ]);
    }
}
