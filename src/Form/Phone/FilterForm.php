<?php
declare(strict_types=1);

namespace App\Form\Phone;

use App\Form\Phone\DTO\Filter;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FilterForm
 * @package App\Form\Phone
 */
class FilterForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('value', SearchType::class, [
            'label' => 'filter_form.label_value',
            'required' => true,
            'attr' => [
                'placeholder' => 'filter_form.placeholder_value',
            ],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'translation_domain' => 'phone',
        ]);
    }
}
