<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class WorkflowSubscriber implements EventSubscriberInterface
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
            CompletedEvent::class => ['workflowCompletedEventSubscriber']
        ];
    }

    /**
     * @param CompletedEvent $event
     * @return void
     */
    public function workflowCompletedEventSubscriber(CompletedEvent $event): void
    {
        $this->addFlash($event->getSubject(), ucfirst('Flashes.' . ucfirst($event->getWorkflowName()) . '.' . ucfirst($event->getTransition()->getName())));
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