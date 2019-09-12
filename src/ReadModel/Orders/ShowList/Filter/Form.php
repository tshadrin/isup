<?php
declare(strict_types=1);

namespace App\ReadModel\Orders\ShowList\Filter;


use App\Entity\Intercom\Status;
use App\ReadModel\DateIntervalTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('text', SearchType::class, [
            'label' => 'filter_form.label.text',
            'required' => false,
            'attr' => [
                'placeholder' => 'filter_form.placeholder.text',
            ],
        ]);
        $builder->add('preset', ChoiceType::class, [
            'label' => 'filter_form.label.presets',
            'choices' => [
                'filter_form.preset.istra' => Filter::PRESET_ISTRA,
                'filter_form.preset.dedovsk' => Filter::PRESET_DEDOVSK,
                'filter_form.preset.dedovsk' => Filter::PRESET_DEDOVSK,
                'filter_form.preset.actual' => Filter::PRESET_ACTUAL,
                'filter_form.preset.outdate' => Filter::PRESET_OUTDATE,
                'filter_form.preset.not_assigned' => Filter::PRESET_NOT_ASSIGNED,
                'filter_form.preset.current_user' => Filter::PRESET_CURRENT_USER,
            ],
            'required' => false,
            'attr' => [
                'placeholder' => 'filter_form.placeholder.preset',
            ],
        ]);
        $builder->add('status', EntityType::class, [
            'label' => 'filter_form.label.status',
            'class' => Status::class,
            'required' => false,
            'attr' => [
                'placeholder' => 'filter_form.placeholder.preset',
            ],
        ]);

        $builder->add('interval',TextType::Class, [
            'label' => 'filter_form.label.interval',
            'required' => false,
            'attr' => [
                'pattern' => '\d{2}-\d{2}-\d{4} - \d{2}-\d{2}-\d{4}',
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
            'translation_domain' => 'netpay',
        ]);
    }
}
