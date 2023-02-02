<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class LoginSubscriber implements EventSubscriberInterface
{
    /**
     * @param NotifierInterface $notifier
     * @param TranslatorInterface $translator
     */
    public function __construct(private NotifierInterface $notifier,
                                private TranslatorInterface $translator)
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
        $this->notifier('Flashes.Login.Login', ucfirst($event->getAuthenticatedToken()->getUser()->getUserIdentifier()));
    }

    /**
     * @param LogoutEvent $event
     * @return void
     */
    public function logoutEventSubscriber(LogoutEvent $event): void
    {
        $this->notifier('Flashes.Login.Logout', ucfirst($event->getToken()->getUserIdentifier()));
    }

    /**
     * @param string $message
     * @param string $username
     * @return void
     */
    private function notifier(string $message, string $username): void
    {
        $this->notifier->send((new Notification($this->translator->trans($message, [
            '%username%' => $username,
        ]), ['browser']))
            ->importance(Notification::IMPORTANCE_LOW));
    }
}