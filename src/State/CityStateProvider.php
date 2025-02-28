<?php

namespace App\State;

use App\Dto\CityRequestDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\PaginatorInterface;
use App\Dto\CityResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $collectionProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Executer une requête de recherche de la ressource portant l'id indiquée en paramètre dynamique dans l'URL
        $data = $this->collectionProvider->provide($operation, $uriVariables, $context); // contenu de la requête

        // S'il n' y a pas de ville correspndante à l'ID
        if ($data instanceof PaginatorInterface && $data->count() > 0) {
            // Créer un DTO et parcourir les informations pour les stocker dans un DTO
            foreach ($data as $key => $value) {

                $city = new CityResponseDto();
                // Remplissage de l'objet CityDTO avec les données de l'entité City
                // dd($value->getId());
                $city->id = $value->getId();
                $city->countryName = $value->getCountry()->getName();
                $city->name = $value->getName();



                // Ajout de l'objet ArticleDto dans le tableau $response à rendre à l'interface API
                $response = $city;
            }
            // dd($response);

            return $response;
        } else {
            throw new NotFoundHttpException('Aucune ville retrouvée avec l\'id fourni');
        }
    }
}
