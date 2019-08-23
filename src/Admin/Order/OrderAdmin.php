<?php
declare(strict_types=1);

namespace App\Admin\Order;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\{ DatagridMapper, ListMapper };
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\{ DateTimeType, TextType };

/**
 * Class OrderAdmin
 * @package App\Admin\Order
 */
class OrderAdmin extends AbstractAdmin
{
    /**
     * Настройка полей формы редактирования задачи
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('id')
            ->add('utmId', TextType::class, ['required' => false])
            ->add('fullName')
            ->add('address')
            ->add('serverName')
            ->add('ip')
            ->add('comment')
            ->add('phone')
            ->add('created', DateTimeType::class)
            ->add('user')
            ->add('executed')
            ->add('deletedId')
            ->add('status')
            ->add('isDeleted')
        ;
    }

    /**
     * Настройка фильтров отображения задач
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('id')
            ->add('utmId')
            ->add('fullName')
            ->add('address')
            ->add('serverName')
            ->add('ip')
            ->add('comment')
            ->add('phone')
            ->add('created','doctrine_orm_date_range')
            ->add('user')
            ->add('executed')
            ->add('deletedId')
            ->add('completed','doctrine_orm_date_range',['input_type'=>'timestamp'])
            ->add('status')
            ->add('isDeleted')
        ;
    }

    /**
     * Настройка отображения полей списка задач
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('id')
            ->add('utmId')
            ->add('fullName')
            ->add('address')
            ->add('serverName')
            ->add('ip')
            ->add('comment')
            ->add('phone')
            ->add('created', 'datetime', ['format' => "d-m-Y H:i"])
            ->add('user')
            ->add('executed')
            ->add('deletedId')
            ->add('completed', 'datetime', ['format' => "d-m-Y H:i"])
            ->add('status')
            ->add('isDeleted')
            ->add('_action', null,
                [
                    'actions' =>
                        [
                            'show' => [],
                            'edit' => [],
                        ]
                ]
            )
        ;

    }

    /**
     * Настройка отображения полей задачи
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('utm_id')
            ->add('full_name')
            ->add('address')
            ->add('server_name')
            ->add('ip')
            ->add('comment')
            ->add('phone')
            ->add('created', 'datetime', ['format' => "d-m-Y H:i"])
            ->add('user')
            ->add('executed')
            ->add('deleted_id')
            ->add('completed', 'datetime', ['format' => "d-m-Y H:i"])
            ->add('status')
            ->add('is_deleted')
        ;
    }
}
