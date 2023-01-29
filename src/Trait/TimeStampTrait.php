<?php

namespace App\Trait;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait TimeStampTrait
{
    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(nullable: false)]
    private ?DateTimeImmutable $created_at = null;

    /**
     * @var DateTimeImmutable|null
     */
    #[ORM\Column(nullable: false)]
    private ?DateTimeImmutable $updated_at = null;

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param ?DateTimeImmutable $created_at
     * @return $this
     */
    public function setCreatedAt(?DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @param ?DateTimeImmutable $updated_at
     * @return $this
     */
    public function setUpdatedAt(?DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return void
     */
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->created_at = $this->created_at ?? new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
    }

    /**
     * @return void
     */
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }
}