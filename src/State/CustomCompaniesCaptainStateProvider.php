<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CompanyCaptainRepository;

class CustomCompaniesCaptainStateProvider implements ProviderInterface
{
    public function __construct(
        private CompanyCaptainRepository $companyCaptainRepository
    ) {}

    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): array {

        return $this->companyCaptainRepository->findBy([
            'company' => $uriVariables['companyId']
        ]);
    }
}
