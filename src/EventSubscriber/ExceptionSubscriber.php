<?php

namespace App\EventSubscriber;

use App\Exception\FileNotFoundException;
use App\Exception\NoContentException;
use App\Exception\UnauthorizedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    protected function getErrorJson(string $message, int $code): JsonResponse
    {
        return new JsonResponse([
            'error' => $message,
        ], $code);
    }

    protected function getErrorResponse(\Throwable $error): JsonResponse
    {
        return match (true) {
            $error instanceof FileNotFoundException => $this->getErrorJson($error->getMessage(), $error->getCode()),
            $error instanceof ExtraAttributesException => $this->getErrorJson($error->getMessage(), 400),
            $error instanceof UnprocessableEntityHttpException => $this->getErrorJson('Unprocessable entity: '.$error->getMessage(), code: 400),
            $error instanceof UnauthorizedException => $this->getErrorJson($error->getMessage(), $error->getCode()),
            $error instanceof NoContentException => new JsonResponse([], 204),
            default => $this->getErrorJson('Internal server error: '.$error->getMessage(), 500),
        };
    }

    public function onException(ExceptionEvent $event): void
    {
        $event->setResponse(
            $this->getErrorResponse($event->getThrowable()),
        );

        $event->allowCustomResponseCode();
    }
}
