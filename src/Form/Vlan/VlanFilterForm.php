<?php
declare(strict_types=1);

namespace App\Form\Vlan;

use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ SearchType, SubmitType };

/**
 * Class VlanFilterForm
 * @package App\Form\Vlan
 */
class VlanFilterForm extends AbstractType
{
    /**
     * Создание формы для поиска платежей
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('search', SearchType::class, [
            'label' => 'vlan_filter_form.search_data',
            'required' => true,
            'attr' => [
                'placeholder' => 'vlan_filter_form.search_placeholder',
                'class' => 'form-control form-control-sm m-1',
            ],
            'label_attr' => [
                'class' => 'col-auto col-form-label col-form-label-sm text-light m-1 pr-1',
            ],
        ]);
        $builder->add('submit_button', SubmitType::class, [
            'label' => 'vlan_filter_form.search_button',
            'attr' => [
                'class' => 'btn btn-primary btn-sm btn-primary-sham m-1',
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'vlan_bundle_vlan_filter_form';
    }
}
