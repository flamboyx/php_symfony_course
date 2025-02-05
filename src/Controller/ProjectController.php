<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/data/projects')]
final class ProjectController extends AbstractController
{
    #[Route('/', name: 'project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): JsonResponse
    {
        $projects = $projectRepository->findAll();
        return $this->json($projects, 200, [], ['groups' => ['project_read']]);
    }

    #[Route('/{id}', name: 'project_read', methods: ['GET'])]
    public function read(Project $project): JsonResponse
    {
        return $this->json(['data' => $project]);
    }

    #[Route('/', name: 'project_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ProjectGroupRepository $projectGroupRepository): JsonResponse
    {
        $project = new Project();
        $groupId = $data['projectGroup'] ?? null;

        if ($groupId) {
            $projectGroup = $projectGroupRepository->find($groupId);

            if ($projectGroup) {
                $project->setProjectGroup($projectGroup);
            } else {
                return $this->json(['error' => 'Project group not found'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json(['error' => 'Project group ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();
            return $this->json($project, Response::HTTP_CREATED, [], ['groups' => ['project_read']]);
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
            return $this->json($project, 200, [], ['groups' => ['project_read']]);
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