<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Repository\FlightRepository;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class DeleteCaptainStateProcessor implements ProcessorInterface
{
    // Injection de dépendance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private ProcessorInterface $removeProcessor,
        private Security $security,
        private FlightRepository $flightRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Vérifier la permission de l'utilisateur avant de supprimer une ressource Commandant de bord
        if (!$this->security->isGranted('CAPTAIN_DELETE', $data)) {
            throw new AccessDeniedException('Accès refusé car vous n\'est ni ADMIN ni auteur des données personnelles');
        }
        // Vérifier si des vols ne sont pas prévus à l'avenir pour le commandant de bord
        $countNextFlightsForCaptain = $this->flightRepository->countNextFlightsForCaptain($data->getId());

        if ($countNextFlightsForCaptain > 0) {
            throw new ConflictHttpException('Suppression impossible. Le commandant de bord a ' . $countNextFlightsForCaptain . ' vol(s) prévus à l\'avenir');
        }

        // Supprimer la ressource
        $this->removeProcessor->process($data, $operation, $uriVariables, $context);

        // Préparer une réponse avec un code de réussite
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
