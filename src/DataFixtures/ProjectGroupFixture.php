<?php

namespace App\DataFixtures;

use App\Entity\ProjectGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectGroupFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $project = new ProjectGroup();
            $project->setName('Project Group' . $i);

            $manager->persist($project);
        }

        $manager->flush();
    }
}