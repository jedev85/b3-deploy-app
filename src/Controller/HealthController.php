<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HealthController extends AbstractController
{
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function __invoke(Connection $connection): JsonResponse
    {
        $database = 'ok';

        try {
            $connection->executeQuery('SELECT 1')->fetchOne();
        } catch (\Throwable) {
            $database = 'error';
        }

        return $this->json([
            'status' => 'ok' === $database ? 'ok' : 'degraded',
            'environment' => $this->getParameter('kernel.environment'),
            'database' => $database,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]);
    }
}
