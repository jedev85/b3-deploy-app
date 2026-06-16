<?php

namespace App\Controller;

use App\Repository\ApplicationRepository;
use App\Repository\DeploymentRequestRepository;
use App\Repository\EnvironmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        ApplicationRepository $applicationRepository,
        EnvironmentRepository $environmentRepository,
        DeploymentRequestRepository $deploymentRequestRepository,
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'application_count' => $applicationRepository->count([]),
            'active_environment_count' => $environmentRepository->countActive(),
            'pending_deployment_count' => $deploymentRequestRepository->countPending(),
            'latest_deployments' => $deploymentRequestRepository->findLatest(),
        ]);
    }
}
