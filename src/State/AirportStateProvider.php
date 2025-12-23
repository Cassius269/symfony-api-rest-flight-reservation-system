<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirportResponseDto;
use App\Dto\CityResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AirportStateProvider implements ProviderInterface
{
    /// Injection de dépendance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Retrieve the state from somewhere
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        // dd($data);

        if (!$data) {
            throw new NotFoundHttpException("Ressource inexistante");
        }

        // Retourner la réponse au client sous forme de DTO
        $airportDto = new AirportResponseDto;
        $airportDto->id = $data->getId();
        $airportDto->name = $data->getName();

        $cityDto = new CityResponseDto;
        $cityDto->name = $data->getCity()->getName();
        $cityDto->countryName = $data->getCity()->getCountry()->getName();

        $airportDto->city = $cityDto;


        return $airportDto;
    }
}
