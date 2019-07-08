<?php
declare(strict_types=1);

namespace App\Admin\User;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\{ DatagridMapper, ListMapper };
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\{ CheckboxType, ChoiceType, PasswordType, RepeatedType, TextType };

/**
 * Class UserAdmin
 * @package App\Admin\User
 */
class UserAdmin extends AbstractAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('full_name', TextType::class)
            ->add('username', TextType::class)
            ->add('email', TextType::class)
            ->add('region', ModelType::class, ['class' => 'App:User\Region',])
            ->add('enabled', CheckBoxType::class, ['required' => false])
            ->add('on_work', CheckBoxType::class, ['required' => false])
            ->add('roles', ChoiceType::class,
                [
                    'choices' => $this->getExistingRoles(),
                    'data' => $this->getSubject()->getRoles(),
                    'label' => 'Roles',
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => true,
                ]
            )
            ->add('bitrixId', TextType::class, ['required' => false,])
            ->add('plainPassword', RepeatedType::class,
                [
                    'required' => false,
                    'type' => PasswordType::class,
                    'options' =>
                        [
                            'required' => 'false',
                            'translation_domain' => 'FOSUserBundle',
                            'attr' =>
                                [
                                    'autocomplete' => 'new-password',
                                ],
                        ],
                    'first_options' => ['label' => 'form.new_password'],
                    'second_options' => ['label' => 'form.new_password_confirmation'],
                    'invalid_message' => 'fos_user.password.mismatch',
                ]
            )
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('fullName')
            ->add('username')
            ->add('email')
            ->add('region')
            ->add('enabled')
            ->add('onWork')
            ->add('roles')
            ->add('bitrixId')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('fullName')
            ->add('email', null, ['editable' => true])
            ->add('username', null, ['editable' => true])
            ->add('region')
            ->add('enabled', null, ['editable' => true])
            ->add('onWork', null, ['editable' => true])
            ->add('last_login', 'datetime')
            ->add('bitrixId', null, ['editable' => true])
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
            ->add('full_name')
            ->add('username')
            ->add('email')
            ->add('on_work')
            ->add('enabled')
            ->add('roles')
            ->add('region')
            ->add('last_login', 'datetime')
            ->add('bitrix_id')
        ;
    }

    /**
     * Используется для инъекции UserManager
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager(): UserManagerInterface
    {
        return $this->userManager;
    }

    /**
     * При создании пользователя создает пароль
     * @param $user
     */
    public function preCreate($user): void
    {
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * При обновлении пользователя обновляет пароль
     * @param $user
     */
    public function preUpdate($user): void
    {
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * Основные роли для формы
     * @return mixed
     */
    public function getExistingRoles(): array
    {
        $mainRoles = [];
        $roleHierarchy = $this->getConfigurationPool()
            ->getContainer()->getParameter('security.role_hierarchy.roles');
        $roles = array_keys($roleHierarchy);
        foreach ($roles as $role) {
            $mainRoles[mb_strtolower($role)] = $role;
        }
        return $mainRoles;
    }
}
