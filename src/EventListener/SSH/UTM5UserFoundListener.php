<?php
namespace App\EventListener\SSH;

use App\Event\UTM5UserFoundEvent;
use Twig\Environment;

/**
 * Class UTM5UserFoundListener
 * @package App\EventListener\SSH
 */
class UTM5UserFoundListener
{
    /*
     * var Environment $templating
     */
    private $templating;

    /**
     * UTM5UserFoundListener constructor.
     * @param Environment $templating
     */
    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    /**
     * Обработчик рендерит шаблон для диагностики
     * @param UTM5UserFoundEvent $event
     */
    public function onUTM5UserFound(UTM5UserFoundEvent $event)
    {
        $user = $event->getUser();
        if(count($user->getIps()) > 0) {
            $diagnostic = $this->templating->render('SSH/diagnostic.html.twig', ['user' => $user]);
        } else {
            $diagnostic = '';
        }
        $event->addResult('diagnostic', $diagnostic);
    }
}
