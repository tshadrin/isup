<?php
namespace App\Admin\Vlan;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VlanAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('number', IntegerType::class);
        $formMapper->add('name', TextType::class);
        $formMapper->add('points', CollectionType::class,[
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            ]);
        $formMapper->add('deleted', null,['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('number');
        $datagridMapper->add('name');
        $datagridMapper->add('points');
        $datagridMapper->add('deleted');
    }

    protected function configureListFields(ListMapper $listMapper)
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

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('number')
            ->add('name')
            ->add('points')
            ->add('deleted')
        ;
    }
}
