<?php
declare(strict_types=1);

namespace App\EventListener\Intercom;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Класс обрабатывает событие авторизации и если у пользователя есть роль ROLE_INTERCOMS
 * перенаправляет его на маршрут intercom_index
 * Class SecurityListener
 * @package App\EventListener\Intercom
 */
class SecurityListener
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var UserCheckerInterface
     */
    protected $tokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * SecurityListener constructor.
     * @param Router $router
     * @param TokenStorageInterface $tokenStorage
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(UrlMatcherInterface $router,
                                TokenStorageInterface $tokenStorage,
                                EventDispatcherInterface $dispatcher)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->dispatcher = $dispatcher;
    }

    /**
     * При событии авторизации метод добавляет обработчик события KernelEvents::RESPONSE
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse']);
    }

    /**
     * ПОлучает список ролей пользователя, если есть роль ROLE_INTERCOMS
     * перенаправляет на маршрут intercom_index
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $roles = $this->tokenStorage->getToken()->getRoleNames();

        if (in_array('ROLE_INTERCOMS', $roles)) {
            $route = $this->router->generate('intercom_index');
            $event->getResponse()->headers->set('Location', $route);
        }
    }
}
