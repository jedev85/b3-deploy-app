<?php

namespace App\Controller;

use App\Entity\DeploymentRequest;
use App\Entity\User;
use App\Form\DeploymentRequestType;
use App\Form\DeploymentStatusType;
use App\Repository\DeploymentRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/deployments')]
#[IsGranted('ROLE_USER')]
class DeploymentRequestController extends AbstractController
{
    #[Route('', name: 'app_deployment_index', methods: ['GET'])]
    public function index(DeploymentRequestRepository $deploymentRequestRepository): Response
    {
        return $this->render('deployment/index.html.twig', [
            'deployments' => $deploymentRequestRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_deployment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $deploymentRequest = new DeploymentRequest();
        $form = $this->createForm(DeploymentRequestType::class, $deploymentRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw $this->createAccessDeniedException();
            }

            $deploymentRequest->setRequestedBy($user);
            $entityManager->persist($deploymentRequest);
            $entityManager->flush();
            $this->addFlash('success', 'Demande de deploiement creee.');

            return $this->redirectToRoute('app_deployment_show', ['id' => $deploymentRequest->getId()]);
        }

        return $this->render('deployment/form.html.twig', [
            'deployment' => $deploymentRequest,
            'form' => $form,
            'title' => 'Nouvelle demande',
        ]);
    }

    #[Route('/{id}', name: 'app_deployment_show', methods: ['GET', 'POST'])]
    public function show(Request $request, DeploymentRequest $deploymentRequest, EntityManagerInterface $entityManager): Response
    {
        $statusForm = $this->createForm(DeploymentStatusType::class, $deploymentRequest);
        $statusForm->handleRequest($request);

        if ($statusForm->isSubmitted()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            if ($statusForm->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Statut mis a jour.');

                return $this->redirectToRoute('app_deployment_show', ['id' => $deploymentRequest->getId()]);
            }
        }

        return $this->render('deployment/show.html.twig', [
            'deployment' => $deploymentRequest,
            'status_form' => $statusForm,
        ]);
    }
}
