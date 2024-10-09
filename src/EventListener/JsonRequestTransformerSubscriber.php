<?php
declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class JsonRequestTransformerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ('' === $request->getContent()) {
            return;
        }

        if (false === $this->isJsonRequest($request)) {
            return;
        }

        if (false === $this->transformJsonBody($request)) {
            throw new BadRequestHttpException('Unable to parse request.');
        }
    }


    private function isJsonRequest(Request $request): bool
    {
        return 'json' === $request->getContentTypeFormat();
    }


    private function transformJsonBody(Request $request): bool
    {
        $data = json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }
        if (null === $data) {
            return true;
        }
        
        $request->request->replace($data);

        return true;
    }
}
