<?php
declare(strict_types=1);

namespace App\Admin\Commutator;

use App\Form\Commutator\PortForm;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\{ DatagridMapper, ListMapper };
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\{ CollectionType, TextType };

/**
 * Class CommutatorAdmin
 * @package App\Admin\Commutator
 */
class CommutatorAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('model', TextType::class)
            ->add('ip', TextType::class)
            ->add('mac', TextType::class)
            ->add('notes', TextType::class)
            ->add('ports', CollectionType::class,
                [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'entry_type' => PortForm::class,
                ]
            )
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name')
            ->add('model')
            ->add('ip')
            ->add('mac')
            ->add('notes')
            ->add('ports')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('id')
            ->add('name', null, ['editable' => true])
            ->add('model', null, ['editable' => true])
            ->add('ip', null, ['editable' => true])
            ->add('mac', null, ['editable' => true])
            ->add('notes', null, ['editable' => true])
            ->add('ports', null, ['editable' => true])
            ->add('_action', null,
                ['actions' =>
                    [
                        'show' => [],
                        'edit' => [],
                        'delete' => [],
                    ]
                ]
            )
        ;

    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('model')
            ->add('ip')
            ->add('mac')
            ->add('notes')
            ->add('ports', 'object', ['sorted' => 'number'])
        ;
    }
}
