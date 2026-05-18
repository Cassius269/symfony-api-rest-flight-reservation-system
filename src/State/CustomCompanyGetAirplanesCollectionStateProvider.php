<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirplaneModelResponseDto;
use App\Dto\AirplaneResponseDto;
use App\Dto\CompanyResponseDto;
use ArrayIterator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomCompanyGetAirplanesCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Retrieve the state from somewhere
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        if ($data instanceof PaginatorInterface) {
            $results = [];


            foreach ($data as $airplane) {
                // dd($airplane);

                $airplaneResponseDto = new AirplaneResponseDto;
                $airplaneResponseDto->id = $airplane->getId();
                $airplaneResponseDto->reference = $airplane->getReference();
                $airplaneResponseDto->airplaneModel = $airplane->getAirplaneModel()->getModel();

                $companyResponseDto = new CompanyResponseDto;
                $companyResponseDto->name = $airplane->getCompany()->getName();
                $airplaneResponseDto->company = $companyResponseDto;

                $airplaneModelResponeDto = new AirplaneModelResponseDto;
                $airplaneModelResponeDto->capacity = $airplane->getAirplaneModel()->getCapacity();
                $airplaneResponseDto->model = $airplaneModelResponeDto;

                $results[] = $airplaneResponseDto;
            }

            // Retourner des résultats paginables avec les options de page courante par exemple
            return new TraversablePaginator(
                new ArrayIterator(($results)),
                $data->getCurrentPage(),
                $data->getItemsPerPage(),
                $data->getTotalItems()
            );
        }
    }
}
