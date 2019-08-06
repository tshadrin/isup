<?php
declare(strict_types = 1);


namespace App\Security\Voter\Bitrix;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserAccess extends Voter
{
    public const PAYCHECK = 'paycheck';
    public const CREATE = 'create';
    public const REMOVE = 'remove';

    const AUTH_NAME = 'auth';

    /**
     * @var string
     */
    private $bitrixMemberId;

    public function __construct(string $bitrixMemberId)
    {
        $this->bitrixMemberId = $bitrixMemberId;
    }

    protected function supports($attribute, $subject): bool
    {
        return
            in_array($attribute, [self::PAYCHECK, self::CREATE, self::REMOVE], true) &&
            $subject instanceof Request;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof Request ||
            !$subject->request->has(self::AUTH_NAME)) {
            return false;
        }

        $auth =  $subject->request->get(self::AUTH_NAME);

        return
            $this->array_keys_exists(['domain', 'member_id'], $auth) &&
            $auth['member_id'] === $this->bitrixMemberId ? true: false;
    }

    private function array_keys_exists(array $keys, array $array): bool
    {
        return !array_diff_key(array_flip($keys), $array);
    }
}