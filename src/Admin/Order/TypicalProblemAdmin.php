<?php
declare(strict_types=1);

namespace App\Admin\Order;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\{ DatagridMapper, ListMapper };
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, DateTimeType, TextareaType, TextType};

class TypicalProblemAdmin extends AbstractAdmin
{
    /**
     * Настройка полей формы редактирования задачи
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('description', TextareaType::class)
            ->add('shortCut')
            ->add('enabled', CheckboxType::class, ['required' => false])
        ;
    }

    /**
     * Настройка фильтров отображения задач
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('id')
            ->add('description')
            ->add('shortCut')
            ->add('enabled')

        ;
    }

    /**
     * Настройка отображения полей списка задач
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('id')
            ->add('description')
            ->add('shortCut')
            ->add('enabled')
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
            ->add('description')
            ->add('shortCut')
            ->add('enabled')
        ;
    }
}
