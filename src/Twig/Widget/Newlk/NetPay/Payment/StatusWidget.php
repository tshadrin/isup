<?php
declare(strict_types = 1);


namespace App\Twig\Widget\Newlk\NetPay\Payment;

use App\ReadModel\Payments\NetPay\Payment;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('payment_status', [$this, 'status'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function status(Environment $twig, Payment $payment): string
    {
        return $twig->render('widget/newlk/netpay/payment/status.html.twig', [
            'payment' => $payment
        ]);
    }
}