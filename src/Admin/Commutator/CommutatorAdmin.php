<?php
namespace App\Admin\Commutator;

use App\Entity\Commutator\Port;
use App\Form\Commutator\PortForm;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommutatorAdmin extends AbstractAdmin
{
    /**
     * Настройка полей формы редактирования задачи
     * @param FormMapper $formMapper
     */

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('model', TextType::class)
            ->add('ip', TextType::class)
            ->add('mac', TextType::class)
            ->add('notes', TextType::class);
        $formMapper->add('ports', CollectionType::class,[
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'entry_type' => PortForm::class,
        ]);
    }

    /**
     * Настройка фильтров задач
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
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
     * Настройка отображения полей списка задач
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name', null, ['editable' => true])
            ->add('model', null, ['editable' => true])
            ->add('ip', null, ['editable' => true])
            ->add('mac', null, ['editable' => true])
            ->add('notes', null, ['editable' => true])
            ->add('ports', null, ['editable' => true])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ])
        ;

    }

    /**
     * Настройка отображения полей задачи
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
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
