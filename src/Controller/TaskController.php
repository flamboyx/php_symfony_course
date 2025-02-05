<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('data/tasks/')]
final class TaskController extends AbstractController
{
    #[Route('/', name: 'task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findAll();
        return $this->json(['data' => $tasks]);
    }

    #[Route('/{id}', name: 'task_read', methods: ['GET'])]
    public function read(Task $task): JsonResponse
    {
        return $this->json(['data' => $task]);
    }

    #[Route('/', name: 'task_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): JsonResponse
    {
        $task = new Task();
        $projectId = $data['project'] ?? null;

        if ($projectId) {
            $project = $projectRepository->find($projectId);

            if ($project) {
                $task->setProject($project);
            } else {
                return $this->json(['error' => 'Project not found'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json(['error' => 'Project ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->json($task, Response::HTTP_CREATED, [], ['groups' => ['task_read']]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'task_update', methods: ['PATCH'])]
    public function update(Request $request, Task $task, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $entityManager->flush();
            return $this->json(['data' => $task]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($task);
        $entityManager->flush();
        return $this->json([], 204);
    }
}