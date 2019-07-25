<?php
declare(strict_types=1);

namespace App\Form\Phone;

use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ SearchType, SubmitType };
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneFilterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('search', SearchType::class, [
            'label' => 'phone_filter_form.search_data',
            'required' => true,
            'attr' => [
                'placeholder' => 'phone_filter_form.search_placeholder',
            ],
        ]);
        $builder->add('submit_button', SubmitType::class, [
            'label' => 'phone_filter_form.search_button',
            'attr' => [
                'class' => 'btn btn-primary btn-sm btn-primary-sham',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
