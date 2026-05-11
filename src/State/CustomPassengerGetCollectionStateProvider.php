<?php

namespace App\State;

use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\Pagination\PaginatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class CustomPassengerGetCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        $passengers = $this->collectionProvider->provide($operation, $uriVariables, $context);

        // Cas où API Platform renvoie une collection paginée
        if ($passengers instanceof PaginatorInterface) {
            $results = [];

            foreach ($passengers as $passenger) {
                $dto = new PassengerResponseDto();
                $dto->id = $passenger->getId();
                $dto->firstname = $passenger->getFirstname();
                $dto->lastname = $passenger->getLastname();
                $dto->email = $passenger->getEmail();

                $results[] = $dto;
            }

            // Retourner un résultat paginable avec les options de page courante par exemple
            return new TraversablePaginator(
                new \ArrayIterator($results),
                $passengers->getCurrentPage(),
                $passengers->getItemsPerPage(),
                $passengers->getTotalItems()
            );
        }
    }
}
