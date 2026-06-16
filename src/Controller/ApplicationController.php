<?php

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/applications')]
#[IsGranted('ROLE_USER')]
class ApplicationController extends AbstractController
{
    #[Route('', name: 'app_application_index', methods: ['GET'])]
    public function index(ApplicationRepository $applicationRepository): Response
    {
        return $this->render('application/index.html.twig', [
            'applications' => $applicationRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_application_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($application);
            $entityManager->flush();
            $this->addFlash('success', 'Application creee.');

            return $this->redirectToRoute('app_application_show', ['id' => $application->getId()]);
        }

        return $this->render('application/form.html.twig', [
            'application' => $application,
            'form' => $form,
            'title' => 'Nouvelle application',
        ]);
    }

    #[Route('/{id}', name: 'app_application_show', methods: ['GET'])]
    public function show(Application $application): Response
    {
        return $this->render('application/show.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Application mise a jour.');

            return $this->redirectToRoute('app_application_show', ['id' => $application->getId()]);
        }

        return $this->render('application/form.html.twig', [
            'application' => $application,
            'form' => $form,
            'title' => 'Modifier une application',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_application_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_application_'.$application->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($application);
            $entityManager->flush();
            $this->addFlash('success', 'Application supprimee.');
        }

        return $this->redirectToRoute('app_application_index');
    }
}
