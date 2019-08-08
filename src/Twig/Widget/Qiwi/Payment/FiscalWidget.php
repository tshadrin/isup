<?php
declare(strict_types = 1);


namespace App\Twig\Widget\Qiwi\Payment;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FiscalWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('qiwi_payment_fiscal', [$this, 'fiscal'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function fiscal(Environment $twig, ?int $fiscal): string
    {
        return $twig->render('widget/qiwi/payment/fiscal.html.twig', [
            'fiscal' => $fiscal,
        ]);
    }
}