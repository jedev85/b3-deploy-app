<?php

namespace App\Entity;

use App\Repository\DeploymentActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DeploymentActivityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DeploymentActivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?DeploymentRequest $deploymentRequest = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $actor = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Choice(choices: DeploymentRequest::STATUSES)]
    private ?string $oldStatus = null;

    #[ORM\Column(length: 30)]
    #[Assert\Choice(choices: DeploymentRequest::STATUSES)]
    private string $newStatus = 'draft';

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    private string $note = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\PrePersist]
    public function initializeTimestamp(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function statusChanged(DeploymentRequest $deploymentRequest, ?User $actor, ?string $oldStatus, string $newStatus): self
    {
        return (new self())
            ->setDeploymentRequest($deploymentRequest)
            ->setActor($actor)
            ->setOldStatus($oldStatus)
            ->setNewStatus($newStatus)
            ->setNote(null === $oldStatus ? 'Demande creee' : 'Statut mis a jour');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeploymentRequest(): ?DeploymentRequest
    {
        return $this->deploymentRequest;
    }

    public function setDeploymentRequest(?DeploymentRequest $deploymentRequest): self
    {
        $this->deploymentRequest = $deploymentRequest;

        return $this;
    }

    public function getActor(): ?User
    {
        return $this->actor;
    }

    public function setActor(?User $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    public function getOldStatus(): ?string
    {
        return $this->oldStatus;
    }

    public function setOldStatus(?string $oldStatus): self
    {
        $this->oldStatus = $oldStatus;

        return $this;
    }

    public function getNewStatus(): string
    {
        return $this->newStatus;
    }

    public function setNewStatus(string $newStatus): self
    {
        $this->newStatus = $newStatus;

        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
