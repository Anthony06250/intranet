<?php

namespace App\EventSubscriber;

use App\Entity\Users;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class EntitySubscriber implements EventSubscriberInterface
{
    /**
     * @param UserPasswordHasherInterface $hasher
     * @param NotifierInterface $notifier
     * @param TranslatorInterface $translator
     */
    public function __construct(private UserPasswordHasherInterface $hasher,
                                private NotifierInterface $notifier,
                                private TranslatorInterface $translator)
    {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforePersistEventSubscriber'],
            BeforeEntityUpdatedEvent::class => ['beforeUpdatedEventSubscriber'],
            AfterEntityPersistedEvent::class => ['afterPersistEventSubscriber'],
            AfterEntityUpdatedEvent::class => ['afterUpdateEventSubscriber'],
            AfterEntityDeletedEvent::class => ['afterDeleteEventSubscriber']
        ];
    }

    /**
     * @param BeforeEntityPersistedEvent $event
     * @return void
     */
    public function beforePersistEventSubscriber(BeforeEntityPersistedEvent $event): void
    {
        if ($event->getEntityInstance() instanceof Users) {
            $this->hashPassword($event->getEntityInstance());
        }
    }

    /**
     * @param BeforeEntityUpdatedEvent $event
     * @return void
     */
    public function beforeUpdatedEventSubscriber(BeforeEntityUpdatedEvent $event): void
    {
        if ($event->getEntityInstance() instanceof Users) {
            $this->hashPassword($event->getEntityInstance());
        }
    }

    /**
     * @param AfterEntityPersistedEvent $event
     * @return void
     */
    public function afterPersistEventSubscriber(AfterEntityPersistedEvent $event): void
    {
        $className = explode('\\', get_class($event->getEntityInstance()));

        $this->notifier('Flashes.' . end($className) . '.Create', $event->getEntityInstance());
    }

    /**
     * @param AfterEntityUpdatedEvent $event
     * @return void
     */
    public function afterUpdateEventSubscriber(AfterEntityUpdatedEvent $event): void
    {
        $className = explode('\\', get_class($event->getEntityInstance()));

        $this->notifier('Flashes.' . end($className) . '.Update', $event->getEntityInstance());
    }

    /**
     * @param AfterEntityDeletedEvent $event
     * @return void
     */
    public function afterDeleteEventSubscriber(AfterEntityDeletedEvent $event): void
    {
        $className = explode('\\', get_class($event->getEntityInstance()));

        $this->notifier('Flashes.' . end($className) . '.Delete', $event->getEntityInstance());
    }

    /**
     * @param mixed $entity
     * @return void
     */
    private function hashPassword(mixed $entity): void
    {
        if ($password = $entity->getPlainPassword()) {
            $entity->setPassword($this->hasher->hashPassword($entity, $password));
        }
    }

    /**
     * @param string $message
     * @param string $instance
     * @return void
     */
    private function notifier(string $message, string $instance): void
    {
        $this->notifier->send((new Notification($this->translator->trans($message, [
            '%instance%' => $instance,
        ]), ['browser']))
            ->importance(Notification::IMPORTANCE_LOW));
    }
}