<?php

namespace App\Entity;

use App\Repository\PublisherSettingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublisherSettingRepository::class)]
class PublisherSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $alias = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'publisherSetting', targetEntity: PublisherDescription::class)]
    private Collection $publisherDescription;

    public function __construct()
    {
        $this->publisherDescription = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, PublisherDescription>
     */
    public function getPublisherDescription(): Collection
    {
        return $this->publisherDescription;
    }

    public function addPublisherDescription(PublisherDescription $publisherDescription): static
    {
        if (!$this->publisherDescription->contains($publisherDescription)) {
            $this->publisherDescription->add($publisherDescription);
            $publisherDescription->setPublisherSetting($this);
        }

        return $this;
    }

    public function removePublisherDescription(PublisherDescription $publisherDescription): static
    {
        if ($this->publisherDescription->removeElement($publisherDescription)) {
            // set the owning side to null (unless already changed)
            if ($publisherDescription->getPublisherSetting() === $this) {
                $publisherDescription->setPublisherSetting(null);
            }
        }

        return $this;
    }
}
