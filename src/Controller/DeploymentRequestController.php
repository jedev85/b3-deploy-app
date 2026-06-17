<?php

namespace App\Controller;

use App\Entity\DeploymentActivity;
use App\Entity\DeploymentComment;
use App\Entity\DeploymentRequest;
use App\Entity\User;
use App\Form\DeploymentCommentType;
use App\Form\DeploymentRequestType;
use App\Form\DeploymentStatusType;
use App\Repository\ApplicationRepository;
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
    public function index(
        Request $request,
        DeploymentRequestRepository $deploymentRequestRepository,
        ApplicationRepository $applicationRepository,
    ): Response {
        $status = $request->query->getString('status') ?: null;
        if ($status && !in_array($status, DeploymentRequest::STATUSES, true)) {
            $status = null;
        }

        $application = null;
        $applicationId = $request->query->getString('application');
        if (ctype_digit($applicationId)) {
            $application = $applicationRepository->find((int) $applicationId);
        }

        return $this->render('deployment/index.html.twig', [
            'deployments' => $deploymentRequestRepository->findForList($status, $application),
            'applications' => $applicationRepository->findBy([], ['name' => 'ASC']),
            'statuses' => DeploymentRequest::STATUSES,
            'current_status' => $status,
            'current_application' => $application,
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
            $entityManager->persist(DeploymentActivity::statusChanged($deploymentRequest, $user, null, $deploymentRequest->getStatus()));
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
        $originalStatus = $deploymentRequest->getStatus();
        $statusForm = $this->createForm(DeploymentStatusType::class, $deploymentRequest);
        $statusForm->handleRequest($request);

        if ($statusForm->isSubmitted()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

            if ($statusForm->isValid()) {
                $user = $this->getUser();
                if (!$user instanceof User) {
                    throw $this->createAccessDeniedException();
                }

                if ($originalStatus !== $deploymentRequest->getStatus()) {
                    $entityManager->persist(DeploymentActivity::statusChanged($deploymentRequest, $user, $originalStatus, $deploymentRequest->getStatus()));
                }

                $entityManager->flush();
                $this->addFlash('success', 'Statut mis a jour.');

                return $this->redirectToRoute('app_deployment_show', ['id' => $deploymentRequest->getId()]);
            }
        }

        $comment = new DeploymentComment();
        $commentForm = $this->createForm(DeploymentCommentType::class, $comment, [
            'action' => $this->generateUrl('app_deployment_comment', ['id' => $deploymentRequest->getId()]),
        ]);

        return $this->render('deployment/show.html.twig', [
            'deployment' => $deploymentRequest,
            'status_form' => $statusForm,
            'comment_form' => $commentForm,
        ]);
    }

    #[Route('/{id}/comments', name: 'app_deployment_comment', methods: ['POST'])]
    public function comment(Request $request, DeploymentRequest $deploymentRequest, EntityManagerInterface $entityManager): Response
    {
        $comment = new DeploymentComment();
        $form = $this->createForm(DeploymentCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user instanceof User) {
                throw $this->createAccessDeniedException();
            }

            $comment
                ->setDeploymentRequest($deploymentRequest)
                ->setAuthor($user);

            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire ajoute.');
        }

        return $this->redirectToRoute('app_deployment_show', ['id' => $deploymentRequest->getId()]);
    }
}
