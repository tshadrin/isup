<?php
declare(strict_types=1);


namespace App\Twig\Extension;


use App\Entity\User\User;
use phpcent\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CentrifugoExtension extends AbstractExtension
{
    const TOKEN_EXPIRES = "+12 hours";

    private $centrifugo;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(Client $centrifugo, TokenStorageInterface $tokenStorage)
    {
        $this->centrifugo = $centrifugo;
        $this->tokenStorage = $tokenStorage;
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('centrifugo_token', [$this, 'token'], ['is_safe' => ['html']]),
        ];
    }
    public function token(): string
    {
        if (!$user = $this->tokenStorage->getToken()->getUser()) {
            return '';
        }
        if (!$user instanceof User) {
            return '';
        }
        return $this->centrifugo->generateConnectionToken(
            $user->getId(),
            (new \DateTime())->modify(self::TOKEN_EXPIRES)->getTimestamp()
        );
    }
}