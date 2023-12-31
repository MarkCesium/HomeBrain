<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: UserLocation::class, cascade: ['remove'])]
    private Collection $userLocations;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Publisher::class, cascade: ['remove'])]
    private Collection $publishers;

    #[ORM\ManyToOne(inversedBy: 'location')]
    private ?IconImage $iconImage = null;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Notice::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $notices;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserApi $UserApi = null;

    public function __construct()
    {
        $this->userLocations = new ArrayCollection();
        $this->publishers = new ArrayCollection();
        $this->notices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<int, UserLocation>
     */
    public function getUserLocations(): Collection
    {
        return $this->userLocations;
    }

    public function addUserLocation(UserLocation $userLocation): static
    {
        if (!$this->userLocations->contains($userLocation)) {
            $this->userLocations->add($userLocation);
            $userLocation->setLocation($this);
        }

        return $this;
    }

    public function removeUserLocation(UserLocation $userLocation): static
    {
        if ($this->userLocations->removeElement($userLocation)) {
            // set the owning side to null (unless already changed)
            if ($userLocation->getLocation() === $this) {
                $userLocation->setLocation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Publisher>
     */
    public function getPublishers(): Collection
    {
        return $this->publishers;
    }

    public function addPublisher(Publisher $publisher): static
    {
        if (!$this->publishers->contains($publisher)) {
            $this->publishers->add($publisher);
            $publisher->setLocation($this);
        }

        return $this;
    }

    public function removePublisher(Publisher $publisher): static
    {
        if ($this->publishers->removeElement($publisher)) {
            // set the owning side to null (unless already changed)
            if ($publisher->getLocation() === $this) {
                $publisher->setLocation(null);
            }
        }

        return $this;
    }

    public function getIconImage(): ?IconImage
    {
        return $this->iconImage;
    }

    public function setIconImage(?IconImage $iconImage): static
    {
        $this->iconImage = $iconImage;

        return $this;
    }

    /**
     * @return Collection<int, Notice>
     */
    public function getNotices(): Collection
    {
        return $this->notices;
    }

    public function addNotice(Notice $notice): static
    {
        if (!$this->notices->contains($notice)) {
            $this->notices->add($notice);
            $notice->setLocation($this);
        }

        return $this;
    }

    public function removeNotice(Notice $notice): static
    {
        if ($this->notices->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getLocation() === $this) {
                $notice->setLocation(null);
            }
        }

        return $this;
    }

    public function getUserApi(): ?UserApi
    {
        return $this->UserApi;
    }

    public function setUserApi(?UserApi $UserApi): static
    {
        $this->UserApi = $UserApi;

        return $this;
    }
}
