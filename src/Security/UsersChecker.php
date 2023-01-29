<?php

namespace App\Security;

use App\Entity\Users;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UsersChecker implements UserCheckerInterface
{
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Users) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException($this->translator->trans('Flash.Users.Inactive', [
                '%user%' => (string) $user
            ]));
        }
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof Users) {
            return;
        }
    }
}