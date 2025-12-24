<?php

namespace App\State;

use App\Repository\CityRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\AirportResponseDto;
use App\Dto\CityResponseDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateAirportProcessor implements ProcessorInterface
{
    // Injection de dépendance
    public function __construct(
        private CityRepository $cityRepository,
        private AirportRepository $airportRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    // public function __
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?AirportResponseDto
    {
        // dd($data);

        $airport = $this->airportRepository->find($uriVariables['id']);
        // dd($airport);

        // Vérifier si la ville existe
        if (isset($data->city)) {
            $isCityExist = $this->cityRepository->findCityByName($data->city->name, $data->city->country);

            if (!$isCityExist) {
                throw new NotFoundHttpException('Ville inexistante dans le pays renseigné');
            }

            $airport->setCity($isCityExist); // modifier la ville de l'aéroport par celui renseigné le client
        }

        $airport->setName($data->name)
            ->setIataCode($data->codeIata)
            ->setUpdatedAt(new \DateTime);

        // Validation des données avant envoi en base de données 
        $errors = $this->validator->validate($airport); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Copilote 

        // dd($errors);
        if (count($errors) > 0) { // s'il y a des erreurs trouvées 
            $errorMessages = [];

            // Générer une erreur 400 (= "bad request") avec les messages d'erreur détaillés 
            foreach ($errors as $error) {
                if ($error->getPropertyPath() != "createdAt") {
                    $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                }
            }

            throw new BadRequestHttpException(json_encode($errorMessages));
        }

        // Persister et envoyer en base de données
        $this->entityManager->persist($airport);
        $this->entityManager->flush();


        // Préparer la réponse à renvoyer au client 
        $airportDto = new AirportResponseDto;
        $airportDto->id = $airport->getId();
        $airportDto->name = $airport->getName();
        $airportDto->codeIata = $airport->getIataCode();
        $airportDto->createdAt = $airport->getCreatedAt();
        $airportDto->updatedAt = $airport->getUpdatedAt();

        $cityDto = new CityResponseDto;
        $cityDto->name = $airport->getCity()->getName();
        $cityDto->countryName = $airport->getCity()->getCountry()->getName();


        $airportDto->city = $cityDto;

        return $airportDto;
    }
}
