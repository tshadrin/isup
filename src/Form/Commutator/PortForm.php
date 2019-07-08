<?php
declare(strict_types=1);

namespace App\Form\Commutator;

use App\Entity\Commutator\{ Commutator, Port, PortType };
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenEntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\{ AbstractType, FormBuilderInterface };
use Symfony\Component\Form\Extension\Core\Type\{ ChoiceType, IntegerType, TextType };
use Symfony\Component\OptionsResolver\OptionsResolver;

class PortForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('number', IntegerType::class)
            ->add("description", TextType::class)
            ->add('type', EntityType::class, ['class' => PortType::class])
            ->add('speed', ChoiceType::class, ['choices' => ['100Mb' => '100', '1Gb' => '1000', '10Gb' => '1000']])
            ->add('commutator', HiddenEntityType::class, ['class' => Commutator::class]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Port::class,
        ]);
    }
}
