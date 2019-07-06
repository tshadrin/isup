<?php
declare(strict_types=1);

namespace App\Admin\Vlan;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\{ ListMapper, DatagridMapper };
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\{ CollectionType, IntegerType, TextType };

class VlanAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('number', IntegerType::class);
        $formMapper->add('name', TextType::class);
        $formMapper->add(
            'points',
            CollectionType::class,[
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                ]
        );
        $formMapper->add('deleted', null, ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('number');
        $datagridMapper->add('name');
        $datagridMapper->add('points');
        $datagridMapper->add('deleted');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('number')
            ->add('name', null, ['editable' => true])
            ->add('points')
            ->add('deleted', null, ['editable' => true])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);

    }

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
