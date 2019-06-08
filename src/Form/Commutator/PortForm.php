<?php

namespace App\Form\Commutator;

use App\Entity\Commutator\Commutator;
use App\Entity\Commutator\Port;
use App\Entity\Commutator\PortType;
use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenEntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PortForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('number', IntegerType::class);
        $builder->add("description", TextType::class);
        $builder->add('type', EntityType::class, ['class' => PortType::class]);
        $builder->add('speed', ChoiceType::class, ['choices' => ['100Mb' => '100', '1Gb' => '1000', '10Gb' => '1000']]);
        $builder->add('commutator', HiddenEntityType::class, ['class' => Commutator::class]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Port::class,
        ]);
    }
}
