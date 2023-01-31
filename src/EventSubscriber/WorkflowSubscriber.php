<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class WorkflowSubscriber implements EventSubscriberInterface
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
            CompletedEvent::class => ['workflowCompletedEventSubscriber']
        ];
    }

    /**
     * @param CompletedEvent $event
     * @return void
     */
    public function workflowCompletedEventSubscriber(CompletedEvent $event): void
    {
        $this->notifier('Flashes.' . ucfirst($event->getWorkflowName()) . '.' . ucfirst($event->getTransition()->getName()),
            $event->getSubject()
        );
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