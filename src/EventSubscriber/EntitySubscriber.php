<?php

namespace App\EventSubscriber;

use App\Entity\Users;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EntitySubscriber implements EventSubscriberInterface
{
    /**
     * @param UserPasswordHasherInterface $hasher
     * @param RequestStack $request
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly UserPasswordHasherInterface $hasher,
                                private readonly RequestStack $request,
                                private readonly TranslatorInterface $translator)
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

        $this->addFlash((string) $event->getEntityInstance(), 'Flashes.' . end($className) . '.Create');
    }

    /**
     * @param AfterEntityUpdatedEvent $event
     * @return void
     */
    public function afterUpdateEventSubscriber(AfterEntityUpdatedEvent $event): void
    {
        $className = explode('\\', get_class($event->getEntityInstance()));

        $this->addFlash((string) $event->getEntityInstance(), 'Flashes.' . end($className) . '.Update');
    }

    /**
     * @param AfterEntityDeletedEvent $event
     * @return void
     */
    public function afterDeleteEventSubscriber(AfterEntityDeletedEvent $event): void
    {
        $className = explode('\\', get_class($event->getEntityInstance()));

        $this->addFlash((string) $event->getEntityInstance(), 'Flashes.' . end($className) . '.Delete');
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
     * @param string $instance
     * @param string $message
     * @return void
     */
    private function addFlash(string $instance, string $message): void
    {
        $this->request->getSession()->getFlashBag()->add('success', $this->translator->trans($message, [
            '%instance%' => $instance,
        ]));
    }
}