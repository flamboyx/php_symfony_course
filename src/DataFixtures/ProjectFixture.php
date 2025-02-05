<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\ProjectGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projectGroups = $manager->getRepository(ProjectGroup::class)->findAll();

        if (empty($projectGroups)) {
            throw new \LogicException('You need to load ProjectGroupFixture first');
        }

        foreach ($projectGroups as $group) {
            for ($i = 0; $i < 3; $i++) {
                $project = new Project();
                $project->setName('Project-' . $i);
                $project->setProjectGroup($group);
                $manager->persist($project);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectGroupFixture::class,
        ];
    }
}