<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CaptainResponseDto;
use App\Dto\CompanyCaptainResponseDto;
use App\Repository\CaptainRepository;
use ArrayIterator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomCompaniesCaptainStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}

    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): object {

        // Récupérer les données
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        // return $data;

        if ($data instanceof PaginatorInterface) {
            $results = [];

            foreach ($data as $companyCaptain) {
                // dd($companyCaptain);
                $companyCaptainResponseDto = new CompanyCaptainResponseDto;
                $companyCaptainResponseDto->company = $companyCaptain->getCompany()->getName();
                $companyCaptainResponseDto->startDate = $companyCaptain->getStartDate();
                $companyCaptainResponseDto->endDate = $companyCaptain->getEndDate();

                $captainResponseDto = new CaptainResponseDto;
                $captainResponseDto->id = $companyCaptain->getCaptain()->getId();
                $captainResponseDto->firstname =  $companyCaptain->getCaptain()->getFirstname();
                $captainResponseDto->lastname = $companyCaptain->getCaptain()->getLastname();
                $captainResponseDto->email = $companyCaptain->getCaptain()->getEmail();

                $companyCaptainResponseDto->captain = $captainResponseDto;

                $results[] = $companyCaptainResponseDto;
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
