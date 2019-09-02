<?php
declare(strict_types=1);

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
     * @param UTM5UserFoundEvent $event
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function onUTM5UserFound(UTM5UserFoundEvent $event): void
    {
        $user = $event->getUser();
        $diagnostic = $user->hasIps() || $user->hasIps6() ?
            $this->templating->render('SSH/diagnostic.html.twig', ['user' => $user]) :
            '';
        $event->addResult('diagnostic', $diagnostic);
    }
}
