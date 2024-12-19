<?php

namespace App\Entity;

use App\Repository\DetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailRepository::class)]
class Detail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Detail')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $qte = null;

    #[ORM\ManyToOne(inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dette $dept = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

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
