<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteTokenProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $response = new JsonResponse(['message' => 'Déconnexion réussie']);
        $response->headers->clearCookie('token', '/');
        $response->headers->clearCookie('refresh_token', '/');

        return $response;
    }
}
