<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\CompanyResponseDto;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InsertCompanyStateProcessor implements ProcessorInterface
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // dd($data);
        $isExistCompany = $this->companyRepository->findOneBy(
            [
                'name' => $data->name,
                'iataCode' => $data->codeIata
            ]
        );

        // dd($isExistCompany);
        if ($isExistCompany) throw new UnprocessableEntityHttpException(message: 'Une compagnie similaire existe');

        if (!$isExistCompany) {
            $company = new Company;
            $company->setName($data->name)
                ->setCreatedAt(new DateTimeImmutable())
                ->setIataCode($data->codeIata);

            // Validation des données avant envoi en base de données 
            $errors = $this->validator->validate($company); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Copilote 

            // Si il n'y a pas d'erreur trouvée
            if ($errors == null) {
                // Envoyer la nouvelle ressource en base de données
                $this->entityManager->persist($company);
                $this->entityManager->flush();

                // Préparer la réponse à retourner au client
                $companyResponseDto = new CompanyResponseDto;
                $companyResponseDto->id =  $company->getId();
                $companyResponseDto->name = $company->getName();
                $companyResponseDto->codeIata = $company->getIataCode();

                return $companyResponseDto; // renvoyer le DTO de réponse en cas de succès d'enregistrement de la nouvelle ressource Copilote
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
}
