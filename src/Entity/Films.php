<?php

namespace App\Entity;

use App\Repository\FilmsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmsRepository::class)]
class Films
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_film = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_sortie = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column(length: 100)]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $realisateur = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $acteurs = null;

    #[ORM\Column(length: 300)]
    private ?string $affiche_url = null;

    public function getId(): ?int
    {
        return $this->id_film;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->date_sortie;
    }

    public function setDateSortie(\DateTimeInterface $date_sortie): static
    {
        $this->date_sortie = $date_sortie;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getRealisateur(): ?string
    {
        return $this->realisateur;
    }

    public function setRealisateur(string $realisateur): static
    {
        $this->realisateur = $realisateur;

        return $this;
    }

    public function getActeurs(): ?string
    {
        return $this->acteurs;
    }

    public function setActeurs(string $acteurs): static
    {
        $this->acteurs = $acteurs;

        return $this;
    }

    public function getAfficheUrl(): ?string
    {
        return $this->affiche_url;
    }

    public function setAfficheUrl(string $affiche_url): static
    {
        $this->affiche_url = $affiche_url;

        return $this;
    }
}
