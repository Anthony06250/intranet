<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @param RequestStack $request
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly RequestStack $request,
                                private readonly TranslatorInterface $translator)
    {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => ['loginEventSubscriber'],
            LogoutEvent::class => ['logoutEventSubscriber']
        ];
    }

    /**
     * @param LoginSuccessEvent $event
     * @return void
     */
    public function loginEventSubscriber(LoginSuccessEvent $event): void
    {
        $this->addFlash($event->getAuthenticatedToken()->getUser()->getUserIdentifier(), 'Flashes.Login.Login');
    }

    /**
     * @param LogoutEvent $event
     * @return void
     */
    public function logoutEventSubscriber(LogoutEvent $event): void
    {
        $this->addFlash($event->getToken()->getUserIdentifier(), 'Flashes.Login.Logout');
    }

    /**
     * @param string $username
     * @param string $message
     * @return void
     */
    private function addFlash(string $username, string $message): void
    {
        $this->request->getSession()->getFlashBag()->add('success', $this->translator->trans($message, [
            '%username%' => ucfirst($username),
        ]));
    }
}