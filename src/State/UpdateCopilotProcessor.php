<?php

namespace App\State;

use App\Dto\CopilotResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Service\HashPasswordService;
use App\Repository\CopilotRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;

class UpdateCopilotProcessor implements ProcessorInterface
{
    // Injection de dépendance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $processor,
        private EntityManagerInterface $entityManager,
        private CopilotRepository $copilotRepository,
        private HashPasswordService $hashPasswordService,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    {

        // Mettre à jour l'objet de type Copilote
        $copilot = $this->copilotRepository->find($uriVariables['id']);

        // // Vérifier la permission de l'utilisateur pour mettre à jour la ressource via un voter
        // if (isset($copilot) && !$this->security->isGranted('COPILOT_EDIT', $copilot)) {
        //     throw new AccessDeniedException('Accès refusé car vous n\'êtes ni ADMIN ni auteur des données personnelles');
        // }

        // dd($copilot);
        if ($copilot) {
            if (isset($data->firstname)) {
                $copilot->setFirstname($data->firstname);
            }

            if (isset($data->lastname)) {
                $copilot->setLastname($data->lastname);
            }

            if (isset($data->email)) {
                $copilot->setEmail($data->email);
            }


            if (isset($data->password)) {
                $this->hashPasswordService->hashPassword($data->password, $copilot);
            }

            $copilot->setUpdatedAt(new \DateTime());

            // Persister et envoyer en base de données
            $this->entityManager->persist($copilot);
            $this->entityManager->flush();
        }
        $this->processor->process($copilot, $operation, $uriVariables, $context);
        // Retourner la réponse au client
        $copilotDtoResponse = new CopilotResponseDto;
        $copilotDtoResponse->id = $copilot->getId();
        $copilotDtoResponse->firstname = $copilot->getLastname();
        $copilotDtoResponse->lastname = $copilot->getLastname();
        $copilotDtoResponse->email = $copilot->getEmail();

        return $copilotDtoResponse;
    }
}
