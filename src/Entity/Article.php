<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?float $prix = null;
    #[ORM\Column]
    private ?int $qte = null;

    /**
     * @var Collection<int, Detail>
     */
    #[ORM\OneToMany(targetEntity: Detail::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $detail;

    public function __construct()
    {
        $this->detail = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Detail>
     */
    public function getDetail(): Collection
    {
        return $this->detail;
    }

    public function addDetail(Detail $detail): static
    {
        if (!$this->detail->contains($detail)) {
            $this->detail->add($detail);
            $detail->setArticle($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): static
    {
        if ($this->detail->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getArticle() === $this) {
                $detail->setArticle(null);
            }
        }

        return $this;
    }
}
