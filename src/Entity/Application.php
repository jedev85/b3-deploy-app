<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Application
{
    public const CRITICALITIES = ['low', 'medium', 'high'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private string $name = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Url]
    private string $repositoryUrl = '';

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $description = '';

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: self::CRITICALITIES)]
    private string $criticality = 'medium';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, Environment> */
    #[ORM\OneToMany(targetEntity: Environment::class, mappedBy: 'application', orphanRemoval: true)]
    private Collection $environments;

    /** @var Collection<int, DeploymentRequest> */
    #[ORM\OneToMany(targetEntity: DeploymentRequest::class, mappedBy: 'application')]
    private Collection $deploymentRequests;

    public function __construct()
    {
        $this->environments = new ArrayCollection();
        $this->deploymentRequests = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function initializeTimestamps(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function refreshUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRepositoryUrl(): string
    {
        return $this->repositoryUrl;
    }

    public function setRepositoryUrl(string $repositoryUrl): self
    {
        $this->repositoryUrl = $repositoryUrl;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCriticality(): string
    {
        return $this->criticality;
    }

    public function setCriticality(string $criticality): self
    {
        $this->criticality = $criticality;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @return Collection<int, Environment> */
    public function getEnvironments(): Collection
    {
        return $this->environments;
    }

    /** @return Collection<int, DeploymentRequest> */
    public function getDeploymentRequests(): Collection
    {
        return $this->deploymentRequests;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
