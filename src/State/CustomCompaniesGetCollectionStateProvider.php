<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CompanyResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomCompaniesGetCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer les ressources dans le serveur
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        // dd($context['filters']);
        // dd($data);
        if ($data instanceof PaginatorInterface) {
            // Préparer la réponse à envoyer au client
            $results = [];

            foreach ($data as $company) {
                $companyResponseDto = new CompanyResponseDto;
                $companyResponseDto->id = $company->getId();
                $companyResponseDto->name = $company->getName();
                $companyResponseDto->codeIata = $company->getIataCode();

                $results[] = $companyResponseDto;
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
