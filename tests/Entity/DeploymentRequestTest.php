<?php

namespace App\Tests\Entity;

use App\Entity\DeploymentRequest;
use PHPUnit\Framework\TestCase;

class DeploymentRequestTest extends TestCase
{
    public function testDeployedStatusSetsDeploymentDate(): void
    {
        $deploymentRequest = new DeploymentRequest();

        self::assertNull($deploymentRequest->getDeployedAt());

        $deploymentRequest->setStatus('deployed');

        self::assertSame('deployed', $deploymentRequest->getStatus());
        self::assertInstanceOf(\DateTimeImmutable::class, $deploymentRequest->getDeployedAt());
    }

    public function testNewDeploymentStartsWithoutCommentsOrActivities(): void
    {
        $deploymentRequest = new DeploymentRequest();

        self::assertCount(0, $deploymentRequest->getComments());
        self::assertCount(0, $deploymentRequest->getActivities());
    }
}
