<?php

namespace App\Repository;

use App\Entity\DeploymentRequest;
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

    /** @return list<DeploymentRequest> */
    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('deployment_request')
            ->addOrderBy('deployment_request.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
