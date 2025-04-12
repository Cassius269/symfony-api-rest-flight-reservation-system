<?php

namespace App\State;

use App\Entity\Copilot;
use ApiPlatform\Metadata\Operation;
use App\Service\HashPasswordService;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CopilotResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InsertCopilotProcessor implements ProcessorInterface
{
    // Injection de dépendance(s)
    public function __construct(
        private ValidatorInterface $validator,
        private HashPasswordService $hashPasswordService,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $processor
    ) {}


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // dd($data);

        // Créer le nouveau copilote
        if ($data) {
            $copilot = new Copilot;
            $copilot->setCreatedAt(new \DateTimeImmutable())
                ->setFirstname($data->firstname)
                ->setLastname($data->lastname)
                ->setEmail($data->email)
                ->setRoles(['ROLE_COPILOT']);

            $this->hashPasswordService->hashPassword($data->password, $copilot); // hasher le mot de passe
        }


        // Validation des données avant envoi en base de données 
        $errors = $this->validator->validate($copilot); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Article 

        if (count($errors) > 0) { // s'il y a des erreurs trouvées 
            $errorMessages = [];
            // Générer une erreur 400 (= "bad request") avec les messages d'erreur détaillés 
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            throw new BadRequestHttpException(json_encode($errorMessages));
        }

        // Envoyer la ressource au serveur
        $this->processor->process($copilot, $operation, $context);

        // Préparer la réponse à renvoyer au client
        $copilotDto = new CopilotResponseDto;
        $copilotDto->id = $copilot->getId();
        $copilotDto->firstname = $copilot->getFirstname();
        $copilotDto->lastname = $copilot->getLastname();
        $copilotDto->email = $copilot->getEmail();
        $copilotDto->createdAt = $copilot->getCreatedAt();

        return $copilotDto;
    }
}
