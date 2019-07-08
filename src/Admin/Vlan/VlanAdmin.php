<?php
declare(strict_types=1);

namespace App\Admin\Vlan;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\{ ListMapper, DatagridMapper };
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\{ CollectionType, IntegerType, TextType };

/**
 * Class VlanAdmin
 * @package App\Admin\Vlan
 */
class VlanAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('number', IntegerType::class)
            ->add('name', TextType::class)
            ->add('points', CollectionType::class,
                [
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                ]
            )
            ->add('deleted', null, ['required' => false])
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('number')
            ->add('name')
            ->add('points')
            ->add('deleted')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('number')
            ->add('name', null, ['editable' => true])
            ->add('points')
            ->add('deleted', null, ['editable' => true])
            ->add('_action', null,
                [
                    'actions' =>
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
            ->add('number')
            ->add('name')
            ->add('points')
            ->add('deleted')
        ;
    }
}
