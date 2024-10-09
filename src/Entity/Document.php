<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DocumentRepository;
use App\Json\JsonPatch;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Index(name: 'modified_at_idx', columns: ['modified_at'])]
class Document
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Ignore]
    private User $user;

    #[ORM\Column(type: 'string')]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(type: 'json')]
    private array $payload = [];

    #[ORM\Column(type: 'datetime_immutable')]
    #[SerializedName('createAt')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[SerializedName('modifyAt')]
    private \DateTimeInterface $modifiedAt;

    public function __construct(string $id, User $user) {
        $this->id = $id;
        $this->user = $user;
        $this->createdAt = $this->modifiedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isOwner(null|User $user): bool
    {
        if (null === $user) {
            return false;
        }

        if ($user !== $this->user) {
            return false;
        }

        return true;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    #[Ignore]
    public function isPublised(): bool
    {
        return self::STATUS_PUBLISHED === $this->status;
    }

    public function publish(): self
    {
        $this->status = self::STATUS_PUBLISHED;
        $this->modifiedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function editPayload(JsonPatch $jsonPatch, array $payload): self
    {
        $jsonPatch->merge($this->payload, $payload);
        $this->modifiedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getModifiedAt(): \DateTimeInterface
    {
        return $this->modifiedAt;
    }
}