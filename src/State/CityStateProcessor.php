<?php

namespace App\State;

use App\Entity\City;
use ApiPlatform\Metadata\Operation;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CityStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CountryRepository $countryRepository,
        private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Rechercher le pays si il est déjà présent dans le serveur
        $isCountryExist = $this->countryRepository->findOneBy([
            'name' => $data->countryName
        ]);

        // dd($data);
        if (!$isCountryExist) {
            throw new NotFoundHttpException("Le pays '{$data->countryName}' n'est pas disponible dans la liste des destinations.");
        }

        // Création d'un nouvel objet City
        // la date de création est gérée par un évènement Doctrine
        $city = new City;
        $city->setCreatedAt(new \DateTimeImmutable())
            ->setName($data->name)
            ->setCountry($isCountryExist);

        // Validation des données avant envoi en base de données 
        $errors = $this->validator->validate($city); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Article 

        // Si il n'y a pas d'erreur trouvée
        if ($errors == null) {
            // Créer la requête et envoyer en base de données
            $this->entityManager->persist($city);
            $this->entityManager->flush();

            return $data;
        }

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
    }
}
