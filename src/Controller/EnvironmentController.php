<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Environment;
use App\Form\EnvironmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class EnvironmentController extends AbstractController
{
    #[Route('/applications/{id}/environments/new', name: 'app_environment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        $environment = (new Environment())->setApplication($application);
        $form = $this->createForm(EnvironmentType::class, $environment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($environment);
            $entityManager->flush();
            $this->addFlash('success', 'Environnement cree.');

            return $this->redirectToRoute('app_application_show', ['id' => $application->getId()]);
        }

        return $this->render('environment/form.html.twig', [
            'application' => $application,
            'environment' => $environment,
            'form' => $form,
            'title' => 'Nouvel environnement',
        ]);
    }

    #[Route('/environments/{id}/edit', name: 'app_environment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Environment $environment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EnvironmentType::class, $environment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Environnement mis a jour.');

            return $this->redirectToRoute('app_application_show', ['id' => $environment->getApplication()?->getId()]);
        }

        return $this->render('environment/form.html.twig', [
            'application' => $environment->getApplication(),
            'environment' => $environment,
            'form' => $form,
            'title' => 'Modifier un environnement',
        ]);
    }

    #[Route('/environments/{id}/delete', name: 'app_environment_delete', methods: ['POST'])]
    public function delete(Request $request, Environment $environment, EntityManagerInterface $entityManager): Response
    {
        $applicationId = $environment->getApplication()?->getId();
        if ($this->isCsrfTokenValid('delete_environment_'.$environment->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($environment);
            $entityManager->flush();
            $this->addFlash('success', 'Environnement supprime.');
        }

        return $this->redirectToRoute('app_application_show', ['id' => $applicationId]);
    }
}
