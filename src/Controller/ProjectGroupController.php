<?php

namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroupType;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/data/project-groups')]
final class ProjectGroupController extends AbstractController
{
    #[Route('/', name: 'project_group_index', methods: ['GET'])]
    public function index(ProjectGroupRepository $projectGroupRepository): JsonResponse
    {
        $projectGroups = $projectGroupRepository->findAll();
        return $this->json(['data' => $projectGroups]);
    }

    #[Route('/{id}', name: 'project_group_read', methods: ['GET'])]
    public function read(ProjectGroup $projectGroup): JsonResponse
    {
        return $this->json($projectGroup, 200, [], ['groups' => ['project_group_read']]);
    }

    #[Route('/', name: 'project_group_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $projectGroup = new ProjectGroup();
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $entityManager->persist($projectGroup);
            $entityManager->flush();
            return $this->json(['data' => $projectGroup], 201);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'project_group_update', methods: ['PATCH'])]
    public function update(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $entityManager->flush();
            return $this->json(['data' => $projectGroup]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'project_group_delete', methods: ['DELETE'])]
    public function delete(ProjectGroup $projectGroup, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($projectGroup);
        $entityManager->flush();
        return $this->json([], 204);
    }
}