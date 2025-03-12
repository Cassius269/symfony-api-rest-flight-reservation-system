<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class RateLimiterSubscriber implements EventSubscriberInterface
{
    // Injection de dépendances 
    public function __construct(
        private RateLimiterFactory $authenticatedApiLimiter
    ) {}

    // Action à executer à chaque requête
    public function onRequestEvent(RequestEvent $event): void
    {
        // ...
        $request = $event->getRequest();

        // dd('une requête est executée par le client');

        $limiter = $this->authenticatedApiLimiter->create($request->getClientIp());

        // Tester si toutes les tentatives sont consommées:  5 tentatives maximums possibles
        if (false === $limiter->consume(1)->isAccepted()) {
            // Création d'une réponse JSON directement
            $response = new JsonResponse([
                'message' => 'Le nombre maximal de requêtes par heure est atteint'
            ], 429); // Code HTTP 429 Beaucoup de requêtes

            $event->setResponse($response);
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
