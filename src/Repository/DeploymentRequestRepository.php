<?php

namespace App\Repository;

use App\Entity\DeploymentRequest;
use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<DeploymentRequest> */
class DeploymentRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeploymentRequest::class);
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('deployment_request')
            ->select('COUNT(deployment_request.id)')
            ->andWhere('deployment_request.status IN (:statuses)')
            ->setParameter('statuses', ['draft', 'ready'])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /** @return array<string, int> */
    public function countByStatus(): array
    {
        $rows = $this->createQueryBuilder('deployment_request')
            ->select('deployment_request.status AS status, COUNT(deployment_request.id) AS total')
            ->groupBy('deployment_request.status')
            ->getQuery()
            ->getArrayResult();

        $counts = array_fill_keys(DeploymentRequest::STATUSES, 0);
        foreach ($rows as $row) {
            $counts[(string) $row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    /** @return list<DeploymentRequest> */
    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('deployment_request')
            ->addOrderBy('deployment_request.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /** @return list<DeploymentRequest> */
    public function findForList(?string $status = null, ?Application $application = null): array
    {
        $queryBuilder = $this->createQueryBuilder('deployment_request')
            ->join('deployment_request.application', 'application')
            ->addSelect('application')
            ->join('deployment_request.targetEnvironment', 'target_environment')
            ->addSelect('target_environment')
            ->join('deployment_request.requestedBy', 'requested_by')
            ->addSelect('requested_by')
            ->addOrderBy('deployment_request.createdAt', 'DESC');

        if ($status) {
            $queryBuilder
                ->andWhere('deployment_request.status = :status')
                ->setParameter('status', $status);
        }

        if ($application) {
            $queryBuilder
                ->andWhere('deployment_request.application = :application')
                ->setParameter('application', $application);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
