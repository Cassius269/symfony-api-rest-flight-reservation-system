<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CaptainResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class CustomCaptainsGetCollectionStateProvider implements ProviderInterface
{
    // Injection de dépendances(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer les commandants de bord
        $data = $this->collectionProvider->provide($operation, $uriVariables, $context);
        // dd($data->count());
        // Renvoyer un message d'erreur si pas de commandant de bord
        if ($data->count() == 0) {
            throw new NotFoundHttpException('Aucun commandant de bord disponible');
        }

        // Construire la réponse à retourner au client (navigateur par exemple)
        $response = [];

        foreach ($data as $captain) {
            // vérifier si l'oobjet commandant de bord n'est pas null ou si c'est réellement un objet
            if (!$captain || !is_object($captain)) {
                continue; // sauter les entrées
            }

            // dd($captain->getId());
            $captainDto = new CaptainResponseDto;
            $captainDto->id = $captain->getId();
            $captainDto->firstname = $captain->getFirstname();
            $captainDto->lastname = $captain->getFirstname();
            $captainDto->email = $captain->getEmail();

            $response[] = $captainDto;
        }

        return $response;
    }
}
