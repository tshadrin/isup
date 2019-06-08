<?php
namespace App\Admin\Commutator;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Раздел администрированя типов задач
 * Class TypeAdmin
 * @package IntercomBundle\Admin
 */
class PortTypeAdmin extends AbstractAdmin {
    /**
     * Настройка полей формы
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class)
            ->add('description', TextType::class)
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
