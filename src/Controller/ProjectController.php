<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/data/projects')]
final class ProjectController extends AbstractController
{
    #[Route('/', name: 'project_id', methods: ['GET'])]
    public function id(ProjectRepository $projectRepository): JsonResponse
    {
        $projects = $projectRepository->findAll();
        return $this->json(['data' => $projects]);
    }

    #[Route('/{id}', name: 'project_read', methods: ['GET'])]
    public function read(Project $project): JsonResponse
    {
        return $this->json(['data' => $project]);
    }

    #[Route('/', name: 'project_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();
            return $this->json(['data' => $project], 201);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'project_update', methods: ['PATCH'])]
    public function update(Request $request, Project $project, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $entityManager->flush();
            return $this->json(['data' => $project]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'project_delete', methods: ['DELETE'])]
    public function delete(Project $project, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($project);
        $entityManager->flush();
        return $this->json([], 204);
    }
}