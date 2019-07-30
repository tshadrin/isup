<?php
declare(strict_types = 1);


namespace App\ReadModel\Payments\NetPay\Filter;


use App\ReadModel\Payments\NetPay\Payment;
use \DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('userId', SearchType::class, [
            'label' => 'filter_form.label.user_id',
            'required' => false,
            'attr' => [
                'placeholder' => 'filter_form.placeholder.user_id',
            ],
        ]);

        $builder->add('status', ChoiceType::class, [
            'choices'  => [
                'filter_form.choice.processed' => Payment::STATUS_PROCESSED,
                'filter_form.choice.incomplete' => Payment::STATUS_INCOMPLETE,
                'filter_form.choice.error' => Payment::STATUS_ERROR,
            ],
            'label' => 'filter_form.label.status',
            'required' => false,
            'attr' => [
                'placeholder' => 'filter_form.placeholder.status',
            ],
        ]);

        $builder->add('interval',TextType::Class, [
            'label' => 'filter_form.label.interval',
            'required' => false,
            'attr' => [
                'pattern' => '\d{2}-\d{2}-\d{4} - \d{2}-\d{2}-\d{4}',
            ],
        ]);

        $builder->get('interval')->addModelTransformer(new CallbackTransformer(
            static function(?array $intervalAsArray): ?string
            {
                if (!is_null($intervalAsArray)) {
                    return "{$intervalAsArray[0]->format('d-m-Y')} - {$intervalAsArray[1]->format('d-m-Y')}";
                }
                return null;
            },
            static function(?string $intervalAsString): ?array
            {
                if (!is_null($intervalAsString)) {
                    [$from, $to] = explode(' - ', $intervalAsString);
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
     * @param OptionsResolver $resolver
     */
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
