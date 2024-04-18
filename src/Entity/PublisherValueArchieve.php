<?php

namespace App\Entity;

use App\Repository\PublisherValueArchieveRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublisherValueArchieveRepository::class)]
class PublisherValueArchieve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated = null;

    #[ORM\ManyToOne(inversedBy: 'publisherValueArchieves')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publisher $publisher = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isValid = null;

    public function __construct(array $data)
    {
        $this->setValue($data['value']);
        $this->setPublisher($data['publisher']);
        $this->setUpdated($data['updated']);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function isIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): static
    {
        $this->isValid = $isValid;

        return $this;
    }
}
