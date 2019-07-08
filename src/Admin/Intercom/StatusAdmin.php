<?php
declare(strict_types=1);

namespace App\Admin\Intercom;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\{ DatagridMapper, ListMapper };
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

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
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('name')
            ->add('description')
        ;
    }

    /**
     * Настройка фильтров
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name')
            ->add('description')
        ;
    }

    /**
     * Настройка отображения полей списка
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('name')
            ->add('description')
        ;
    }

    /**
     * Настройка отображения полей
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper->add('name')
            ->add('description')
        ;
    }
}
