<?php
declare(strict_types=1);

namespace App\Admin\Intercom;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\{ DatagridMapper, ListMapper };
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\{ CheckboxType, TextareaType, TextType };

/**
 * Class TaskAdmin
 * @package App\Admin\Intercom
 */
class TaskAdmin extends AbstractAdmin
{
    /**
     * Настройка полей формы редактирования задачи
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('phone', TextType::class)
            ->add('fullname', TextType::class)
            ->add('address', TextType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('user', ModelType::class, ['class' => 'App:User\User',])
            ->add('status', ModelType::class, ['class' => 'App:Intercom\Status',])
            ->add('type', ModelType::class, ['class' => 'App:Intercom\Type',])
            ->add('deleted', CheckboxType::class, ['required' => false])
        ;
    }

    /**
     * Настройка фильтров отображения задач
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('phone')
            ->add('fullname')
            ->add('address')
            ->add('description')
            ->add('user')
            ->add('status')
            ->add('type')
            ->add('created','doctrine_orm_date_range')
            ->add('deleted')
        ;
    }

    /**
     * Настройка отображения полей списка задач
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('id')
            ->add('phone')
            ->add('fullName')
            ->add('address')
            ->add('description')
            ->add('user')
            ->add('status')
            ->add('type')
            ->add('created', 'datetime', ['format' => "d-m-Y H:i"])
            ->add('completed','datetime', ['format' => "d-m-Y H:i"])
            ->add('deleted')
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
            ->add('full_name')
            ->add('address')
            ->add('description')
            ->add('user')
            ->add('status')
            ->add('type')
            ->add('created')
            ->add('deleted')
        ;
    }
}
