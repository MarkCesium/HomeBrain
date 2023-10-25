<?php

namespace App\Entity;

use App\Repository\PublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class Publisher
 * @package App\Entity
 */
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

    #[ORM\OneToMany(mappedBy: 'publisher', targetEntity: PublisherValueArchieve::class, orphanRemoval: true)]
    private Collection $publisherValueArchieves;

    public function __construct()
    {
        $this->publisherDescriptions = new ArrayCollection();
        $this->publisherValueArchieves = new ArrayCollection();
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @param Location|null $location
     * @return $this
     */
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

    /**
     * @param PublisherDescription $publisherDescription
     * @return $this
     */
    public function addPublisherDescription(PublisherDescription $publisherDescription): static
    {
        if (!$this->publisherDescriptions->contains($publisherDescription)) {
            $this->publisherDescriptions->add($publisherDescription);
            $publisherDescription->setPublisher($this);
        }

        return $this;
    }

    /**
     * @param PublisherDescription $publisherDescription
     * @return $this
     */
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

    /**
     * @param string $responseType
     * @return $this
     */
    public function setResponseType(string $responseType): static
    {
        $this->responseType = $responseType;

        return $this;
    }

    /**
     * @return array
     */
    public function getAsArrayAPI(): array
    {
        $publisherDescriptions = [];
        $descriptions = $this->getPublisherDescriptions();
        foreach ($descriptions as $pd) {
            $setting = $pd->getPublisherSetting();
            if ($setting->getFieldsGroup() === 'activation') {
                $publisherDescriptions[$setting->getAlias()] = $pd->getValue();
            }
        }

        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'publisherDescriptions' => $publisherDescriptions
        ];
    }

    /**
     * @return array
     */
    public function getAsArray(): array
    {

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'location' => $this->location->getId(),
            'responseType' => $this->getResponseType(),
            'value' => null,
            'updated' => null
        ];
    }
    // /**+enter=phpDoc,
    // ctrl+alt+shift+T=refactor,
    // alt+click=cursor,
    // ctrl+shift+alt+J=equals words,
    // ctrl+alt+M=extract into new function

    /**
     * @return Collection<int, PublisherValueArchieve>
     */
    public function getPublisherValueArchieves(): Collection
    {
        return $this->publisherValueArchieves;
    }

    public function addPublisherValueArchiefe(PublisherValueArchieve $publisherValueArchiefe): static
    {
        if (!$this->publisherValueArchieves->contains($publisherValueArchiefe)) {
            $this->publisherValueArchieves->add($publisherValueArchiefe);
            $publisherValueArchiefe->setPublisher($this);
        }

        return $this;
    }

    public function removePublisherValueArchiefe(PublisherValueArchieve $publisherValueArchiefe): static
    {
        if ($this->publisherValueArchieves->removeElement($publisherValueArchiefe)) {
            // set the owning side to null (unless already changed)
            if ($publisherValueArchiefe->getPublisher() === $this) {
                $publisherValueArchiefe->setPublisher(null);
            }
        }

        return $this;
    }
}
