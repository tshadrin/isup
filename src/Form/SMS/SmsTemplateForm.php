<?php
declare(strict_types=1);

namespace App\Form\SMS;

use App\Entity\SMS\SmsTemplate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ HiddenType, TextType, SubmitType };
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmsTemplateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('phone', TextType::class, [
            'attr' => [
                'class' => 'form-control form-control-sm',
                'placeholder' => 'Number 9251234567..',
                ],
            ]
        );
        $builder->add('smsTemplate', EntityType::class, [
            'class' => SmsTemplate::class,
            'attr' => [
                'class' => 'form-control form-control-sm',
                ],
            ]
        );
        $builder->add('utmId', HiddenType::class);
        $builder->add('send',
        SubmitType::class,
        [
            'label' => 'Send SMS',
            'attr' => [
                'class' => 'btn btn-primary btn-sm btn-primary-sham',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SmsTemplateData::class,
        ]);
    }
}
