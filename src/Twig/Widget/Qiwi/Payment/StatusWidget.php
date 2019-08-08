<?php
declare(strict_types = 1);


namespace App\Twig\Widget\Qiwi\Payment;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('qiwi_payment_status', [$this, 'status'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function status(Environment $twig, ?int $status): string
    {
        return $twig->render('widget/qiwi/payment/status.html.twig', [
            'status' => $status
        ]);
    }
}