<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirplaneResponseDto;
use App\Dto\CompanyResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AirplaneStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?AirplaneResponseDto
    {
        $data = $this->itemProvider->provide($operation, $uriVariables, $context); // récupération de la ressource complète
        // dd($data);

        if (!$data) {
            throw new NotFoundHttpException('Aucun avion retrouvé avec l\'id fourni');
        }

        $airplaneResponseDto = new AirplaneResponseDto;
        $airplaneResponseDto->id = $data->getId();
        $airplaneResponseDto->reference = $data->getReference();
        $airplaneResponseDto->airplaneModel = $data->getAirplaneModel()->getModel();

        // Préparer le DTO de compagnie
        $companyResponseDto = new CompanyResponseDto;
        $companyResponseDto->name = $data->getCompany()->getName();
        $companyResponseDto->codeIata = $data->getCompany()->getIataCode();

        $airplaneResponseDto->company = $companyResponseDto;
        return $airplaneResponseDto;
    }
}
