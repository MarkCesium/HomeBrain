<?php

namespace App\Entity;

use App\Repository\PublisherDescriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublisherDescriptionRepository::class)]
class PublisherDescription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'publisherDescriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publisher $publisher = null;

    #[ORM\ManyToOne(inversedBy: 'publisherDescription')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PublisherSetting $publisherSetting = null;

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

    public function getPublisher(): ?Publisher
    {
        return $this->publisher;
    }

    public function setPublisher(?Publisher $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getPublisherSetting(): ?PublisherSetting
    {
        return $this->publisherSetting;
    }

    public function setPublisherSetting(?PublisherSetting $publisherSetting): static
    {
        $this->publisherSetting = $publisherSetting;

        return $this;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        return [
            'id' => $this->getPublisher()->getId(),
            'alias' => $this->getPublisherSetting()->getAlias(),
            'value' => $this->getValue()
        ];
    }
}
