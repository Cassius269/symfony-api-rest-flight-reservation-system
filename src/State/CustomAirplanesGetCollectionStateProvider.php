<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirplaneModelResponseDto;
use App\Dto\AirplaneResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomAirplanesGetCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer les ressources dans le serveur
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        // dd($data);
        if ($data instanceof PaginatorInterface) {
            // Préparer la réponse à envoyer au client
            $results = [];

            foreach ($data as $airplane) {
                // dd($airplane);
                $airplaneResponseDto = new AirplaneResponseDto;
                $airplaneResponseDto->id = $airplane->getId();
                $airplaneResponseDto->reference = $airplane->getReference();
                $airplaneResponseDto->airplaneModel = $airplane->getAirplaneModel()->getModel();

                // Récupérer la compagnie
                // $companyResponseDto = new CompanyResponseDto;
                // $companyResponseDto->name = $company->getName();
                // $companyResponseDto->codeIata = $company->getIataCode();

                // Préparer le DTO du modèle de l'avion
                $airplaneModelResponseDto = new AirplaneModelResponseDto;
                $airplaneModelResponseDto->capacity = $airplane->getAirplaneModel()->getCapacity();
                $airplaneResponseDto->model = $airplaneModelResponseDto;


                $results[] = $airplaneResponseDto;
            }


            // Retourner des résultats paginables avec les options de page courante par exemple
            return new TraversablePaginator(
                new \ArrayIterator(($results)),
                $data->getCurrentPage(),
                $data->getItemsPerPage(),
                $data->getTotalItems()
            );
        }
    }
}
