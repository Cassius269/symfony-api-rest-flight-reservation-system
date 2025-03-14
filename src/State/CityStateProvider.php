<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CityResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer la ville à l'aide de son ID entré dans l'URL
        $city = $this->itemProvider->provide($operation, $uriVariables, $context); // contenu de la requête

        if (!$city) {
            throw new NotFoundHttpException('Aucune ville trouvée avec l\'id fourni');
        }

        // Créer un DTO de réponse
        $cityDto = new CityResponseDto();
        $cityDto->id = $city->getId();
        $cityDto->countryName = $city->getCountry()->getName();
        $cityDto->name = $city->getName();

        return $cityDto; // retourner au client le DTO au lieu de l'objet issu de l'entité City
    }
}
