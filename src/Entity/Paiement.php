<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'paiement', cascade: ['persist', 'remove'])]
    private ?Client $client = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $datePaiement = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    private ?Dette $dept = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeImmutable
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeImmutable $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getDept(): ?Dette
    {
        return $this->dept;
    }

    public function setDept(?Dette $dept): static
    {
        $this->dept = $dept;

        return $this;
    }
}
