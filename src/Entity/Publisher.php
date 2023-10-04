<?php

namespace App\Entity;

use App\Repository\PublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublisherRepository::class)]
class Publisher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'publishers')]
    private ?Location $location = null;

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: PublisherDescription::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $publisherDescriptions;

    #[ORM\Column(length: 255)]
    private ?string $responseType = null;

    public function __construct()
    {
        $this->publisherDescriptions = new ArrayCollection();
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, PublisherDescription>
     */
    public function getPublisherDescriptions(): Collection
    {
        return $this->publisherDescriptions;
    }

    public function addPublisherDescription(PublisherDescription $publisherDescription): static
    {
        if (!$this->publisherDescriptions->contains($publisherDescription)) {
            $this->publisherDescriptions->add($publisherDescription);
            $publisherDescription->setPublisher($this);
        }

        return $this;
    }

    public function removePublisherDescription(PublisherDescription $publisherDescription): static
    {
        if ($this->publisherDescriptions->removeElement($publisherDescription)) {
            // set the owning side to null (unless already changed)
            if ($publisherDescription->getPublisher() === $this) {
                $publisherDescription->setPublisher(null);
            }
        }

        return $this;
    }

    public function getResponseType(): ?string
    {
        return $this->responseType;
    }

    public function setResponseType(string $responseType): static
    {
        $this->responseType = $responseType;

        return $this;
    }

    public function getAsArray(): array
    {

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'location' => $this->location->getId(),
            'responseType' => $this->getResponseType()
        ];
    }
}
