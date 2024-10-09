<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ('json' !== $event->getRequest()->getContentTypeFormat()) {
            return;
        }

        $code = 500;
        if ($event instanceof HttpExceptionInterface) {
            $code = $event->getStatusCode();
        }
        $event->setResponse(
            new JsonResponse(['message' => $event->getThrowable()->getMessage()], $code)
        );
    }
}
