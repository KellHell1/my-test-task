<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 125)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateBirth = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MobilePhone::class)]
    private Collection $phoneNumbers;

    public function __construct()
    {
        $this->phoneNumbers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateBirth(): ?\DateTimeInterface
    {
        return $this->dateBirth;
    }

    public function setDateBirth(\DateTimeInterface $dateBirth): self
    {
        $this->dateBirth = $dateBirth;

        return $this;
    }

    /**
     * @return Collection<int, MobilePhone>
     */
    public function getPhoneNumbers(): Collection
    {
        return $this->phoneNumbers;
    }

    public function addPhoneNumber(MobilePhone $phoneNumber): self
    {
        if (!$this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->add($phoneNumber);
            $phoneNumber->setUser($this);
        }

        return $this;
    }

    public function removePhoneNumber(MobilePhone $phoneNumber): self
    {
        if ($this->phoneNumbers->removeElement($phoneNumber)) {
            // set the owning side to null (unless already changed)
            if ($phoneNumber->getUser() === $this) {
                $phoneNumber->setUser(null);
            }
        }

        return $this;
    }
}
