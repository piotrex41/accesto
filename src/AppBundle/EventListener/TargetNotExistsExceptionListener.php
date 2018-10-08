<?php

namespace AppBundle\EventListener;

use AppBundle\Exception\TargetNotExistsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class TargetNotExistsExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof TargetNotExistsException) {
            throw new $exception;
        }

        $httpCode = 500;
        if ($exception->getCode() != 0) {
            $httpCode = $exception->getCode();
        }

        $responseData = [
            'error' => [
                'code' => $httpCode,
                'message' => $exception->getMessage()
            ]
        ];

        $event->setResponse(new JsonResponse($responseData, $httpCode));
    }
}