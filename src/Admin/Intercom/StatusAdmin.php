<?php
namespace App\Admin\Intercom;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Раздел администрирования статусов задач
 * Class StatusAdmin
 * @package App\Admin\Intercom
 */
class StatusAdmin extends AbstractAdmin
{
    /**
     * Настройка полей формы
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name')
            ->add('description')
        ;
    }

    /**
     * Настройка фильтров
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
            ->add('description')
        ;
    }

    /**
     * Настройка отображения полей списка
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('description')
        ;
    }

    /**
     * Настройка отображения полей
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('description')
        ;
    }
}
