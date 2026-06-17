<?php

namespace App\Controller;

use App\Entity\DeploymentRequest;
use App\Repository\DeploymentRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DeploymentStatusApiController extends AbstractController
{
    #[Route('/api/deployment-statuses', name: 'api_deployment_statuses', methods: ['GET'])]
    public function __invoke(DeploymentRequestRepository $deploymentRequestRepository): JsonResponse
    {
        $labels = [
            'draft' => 'Brouillon',
            'ready' => 'Pret',
            'deployed' => 'Deploye',
            'failed' => 'Echec',
            'cancelled' => 'Annule',
        ];

        $counts = $deploymentRequestRepository->countByStatus();
        $total = array_sum($counts);

        $statuses = array_map(static function (string $status) use ($counts, $labels, $total): array {
            $count = $counts[$status] ?? 0;

            return [
                'status' => $status,
                'label' => $labels[$status],
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0.0,
            ];
        }, DeploymentRequest::STATUSES);

        return $this->json([
            'total' => $total,
            'statuses' => $statuses,
        ]);
    }
}
