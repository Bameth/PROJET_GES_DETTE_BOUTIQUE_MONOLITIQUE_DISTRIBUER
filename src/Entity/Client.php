<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le surname ne doit pas être vide.')]
    #[Assert\Length(max: 50, maxMessage: 'Le surname ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $surname = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone ne doit pas être vide.')]
    #[Assert\Length(max: 50, maxMessage: 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\Regex(pattern: '/^\+?\d{9,12}$/', message: 'Le numéro de téléphone doit être valide et contenir entre 9 et 12 chiffres.')]
    private ?string $telephone = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "L'adresse ne doit pas être vide.")]
    private ?string $adresse = null;
    private ?string $image = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;


    /**
     * @var Collection<int, Dette>
     */
    #[ORM\OneToMany(targetEntity: Dette::class, mappedBy: 'client', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $dettes;

    #[ORM\OneToOne(inversedBy: 'client', cascade: ['persist', 'remove'])]
    #[Assert\Type(type: User::class)]
    #[Assert\Valid(groups: ['WITH_COMPTE'])]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?Paiement $paiement = null;


    public function __construct()
    {
        $this->dettes = new ArrayCollection();
        $this->createAt = new \DateTimeImmutable();
        $this->updateAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection<int, Dette>
     */
    public function getDettes(): Collection
    {
        return $this->dettes;
    }

    public function addDette(Dette $dette): static
    {
        if (!$this->dettes->contains($dette)) {
            $this->dettes->add($dette);
            $dette->setClient($this);
        }

        return $this;
    }
    // Client.php

    public function getTotalDette(): float
    {
        $total = 0.0;
        foreach ($this->dettes as $dette) {
            $total += ($dette->getMontant() - $dette->getMontantVerser());
        }
        return $total;
    }
    


    public function removeDette(Dette $dette): static
    {
        if ($this->dettes->removeElement($dette)) {
            // set the owning side to null (unless already changed)
            if ($dette->getClient() === $this) {
                $dette->setClient(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $users): static
    {
        // unset the owning side of the relation if necessary
        if ($users === null && $this->user !== null) {
            $this->user->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($users !== null && $users->getClient() !== $this) {
            $users->setClient($this);
        }

        $this->user = $users;

        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): static
    {
        // unset the owning side of the relation if necessary
        if ($paiement === null && $this->paiement !== null) {
            $this->paiement->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($paiement !== null && $paiement->getClient() !== $this) {
            $paiement->setClient($this);
        }

        $this->paiement = $paiement;

        return $this;
    }
}
