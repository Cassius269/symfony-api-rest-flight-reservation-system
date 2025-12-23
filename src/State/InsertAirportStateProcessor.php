<?php

namespace App\State;

use App\Entity\City;
use App\Entity\Airport;
use App\Entity\Country;
use App\Dto\CityResponseDto;
use App\Dto\AirportResponseDto;
use App\Repository\CityRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\AirportRepository;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Mime\Message;

class InsertAirportStateProcessor implements ProcessorInterface
{
    // Injection de dépendance
    public function __construct(
        private AirportRepository $airportRepository,
        private CityRepository $cityRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private CountryRepository $countryRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        //  dd($data);

        $isCityExist = $this->cityRepository->findOneBy(
            [
                'name' => $data->city->name,
                'zipCode' => $data->city->zipCode
            ]
        );

        // dd($isCityExist);

        // Vérifier si l'aéroport n'existe pas déjà dans la ville choisie
        $isAirportExist = $this->airportRepository->findOneBy([
            'name' => $data->name,
            'city' => $isCityExist
        ]);

        // dd($isAirportExist);

        // Si l'aéroprt existe, envoyer un message d'erreur
        if ($isAirportExist) {
            throw new UnprocessableEntityHttpException(message: 'Un aéroport similaire existe dans la même ville');
        }

        $isExistCountry = $this->countryRepository->findOneBy(['name' => $data->city->country]);

        // dd($isExistCountry);

        // Si le pays n'existe, renvoyer une exception
        if ($isExistCountry == null) {
            throw new UnprocessableEntityHttpException("Le pays renseigné " . $data->city->country . " n'existe pas dans le système");
        }


        // Si la ville n'existe pas, renvoyer une exception
        if ($isCityExist == null) {
            throw new UnprocessableEntityHttpException("La ville renseignée " . $data->city->name . " n'existe pas dans le système");
        }


        // Si l'aéroport n'existe pas, en créer
        $airport = new Airport;
        $airport->setName($data->name)
            ->setIataCode($data->codeIata)
            ->setCreatedAt(new \DateTimeImmutable());

        if ($isCityExist) {
            $airport->setCity($isCityExist);
        }

        // Vérifier les contraintes de validation avant d'envoyer la ressource au serveur
        $errors = $this->validator->validate($airport);

        if (count($errors ?? []) > 0) {
            throw new ValidationException((string) $errors);
        }

        // Persister et envoyer au serveur de base de données
        $this->entityManager->persist($airport);
        $this->entityManager->flush();

        // Retourner une réponse au client sous forme de DTO
        $cityResponseDto = new CityResponseDto;
        $cityResponseDto->name = $airport->getCity()->getName();
        $cityResponseDto->countryName = $airport->getCity()->getCountry()->getName();
        $cityResponseDto->zipCode = $airport->getCity()->getZipCode();;

        $airportResponseDto = new AirportResponseDto;
        $airportResponseDto->id = $airport->getId();
        $airportResponseDto->name = $airport->getName();
        $airportResponseDto->codeIata = $airport->getIataCode();
        $airportResponseDto->city = $cityResponseDto;
        $airportResponseDto->createdAt = $airport->getCreatedAt();

        // dd($airportResponseDto);

        return $airportResponseDto;
    }
}
