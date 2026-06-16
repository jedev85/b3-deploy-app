<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\DeploymentRequest;
use App\Entity\Environment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setEmail('admin@deploylab.test')
            ->setFullName('Admin DeployLab')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $user = (new User())
            ->setEmail('user@deploylab.test')
            ->setFullName('Utilisateur DeployLab')
            ->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $portal = (new Application())
            ->setName('Customer Portal')
            ->setRepositoryUrl('https://github.com/example/customer-portal')
            ->setDescription('Portail client expose aux equipes support et comptes clefs.')
            ->setCriticality('high');
        $manager->persist($portal);

        $billing = (new Application())
            ->setName('Billing Worker')
            ->setRepositoryUrl('https://github.com/example/billing-worker')
            ->setDescription('Traitements asynchrones de facturation et rapprochement.')
            ->setCriticality('medium');
        $manager->persist($billing);

        $portalLocal = (new Environment())
            ->setApplication($portal)
            ->setName('local')
            ->setUrl('http://localhost:8080')
            ->setType('local')
            ->setIsActive(true);
        $manager->persist($portalLocal);

        $portalStaging = (new Environment())
            ->setApplication($portal)
            ->setName('staging')
            ->setUrl('https://staging.portal.deploylab.test')
            ->setType('staging')
            ->setIsActive(true);
        $manager->persist($portalStaging);

        $portalProduction = (new Environment())
            ->setApplication($portal)
            ->setName('production')
            ->setUrl('https://portal.deploylab.test')
            ->setType('production')
            ->setIsActive(true);
        $manager->persist($portalProduction);

        $billingStaging = (new Environment())
            ->setApplication($billing)
            ->setName('staging')
            ->setUrl('https://staging.billing.deploylab.test')
            ->setType('staging')
            ->setIsActive(true);
        $manager->persist($billingStaging);

        $manager->persist((new DeploymentRequest())
            ->setTitle('Deployer Customer Portal 1.8.0')
            ->setDescription('Mise en ligne du nouveau parcours de renouvellement client.')
            ->setApplication($portal)
            ->setTargetEnvironment($portalStaging)
            ->setVersion('1.8.0')
            ->setStatus('ready')
            ->setScheduledAt(new \DateTimeImmutable('+2 days'))
            ->setRequestedBy($user));

        $manager->persist((new DeploymentRequest())
            ->setTitle('Rejouer les jobs Billing Worker 2.3.1')
            ->setDescription('Correctif sur le calcul de TVA intracommunautaire.')
            ->setApplication($billing)
            ->setTargetEnvironment($billingStaging)
            ->setVersion('2.3.1')
            ->setStatus('draft')
            ->setRequestedBy($admin));

        $manager->flush();
    }
}
