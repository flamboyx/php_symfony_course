<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projects = $manager->getRepository(Project::class)->findAll();

        if (empty($projects)) {
            throw new \LogicException('You need to load ProjectFixture first');
        }

        foreach ($projects as $project) {
            for ($i = 0; $i < 10; $i++) {
                $task = new Task();
                $task->setName('Task-' . $i);
                $task->setDescription('Description of task-' . $i);
                $task->setProject($project);
                $manager->persist($task);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixture::class,
        ];
    }
}
