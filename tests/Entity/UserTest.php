<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testEveryUserHasRoleUser(): void
    {
        $user = (new User())->setRoles([]);

        self::assertContains('ROLE_USER', $user->getRoles());
    }

    public function testEmailIsNormalized(): void
    {
        $user = (new User())->setEmail(' Admin@DeployLab.TEST ');

        self::assertSame('admin@deploylab.test', $user->getEmail());
    }
}
