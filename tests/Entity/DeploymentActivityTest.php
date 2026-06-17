<?php

namespace App\Tests\Entity;

use App\Entity\DeploymentActivity;
use App\Entity\DeploymentRequest;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class DeploymentActivityTest extends TestCase
{
    public function testStatusChangedFactoryBuildsActivityEntry(): void
    {
        $deploymentRequest = new DeploymentRequest();
        $actor = (new User())->setEmail('admin@deploylab.test')->setFullName('Admin');

        $activity = DeploymentActivity::statusChanged($deploymentRequest, $actor, 'draft', 'ready');

        self::assertSame($deploymentRequest, $activity->getDeploymentRequest());
        self::assertSame($actor, $activity->getActor());
        self::assertSame('draft', $activity->getOldStatus());
        self::assertSame('ready', $activity->getNewStatus());
        self::assertSame('Statut mis a jour', $activity->getNote());
    }

    public function testStatusChangedFactoryHandlesCreationEntry(): void
    {
        $activity = DeploymentActivity::statusChanged(new DeploymentRequest(), null, null, 'draft');

        self::assertNull($activity->getOldStatus());
        self::assertSame('draft', $activity->getNewStatus());
        self::assertSame('Demande creee', $activity->getNote());
    }
}
