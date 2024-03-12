<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
class Joueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("joueur")]
    private ?int $id = null;

    #[Groups("joueur")]
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups("joueur")]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Groups("joueur")]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Groups("joueur")]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    #[Groups("joueur")]
    private ?string $poste = null;

    #[ORM\ManyToOne(inversedBy: 'joueurs')]
    #[Groups("joueur")]
    private ?Equipe $equipe_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): static
    {
        $this->poste = $poste;

        return $this;
    }

    public function getEquipeId(): ?equipe
    {
        return $this->equipe_id;
    }

    public function setEquipeId(?equipe $equipe_id): static
    {
        $this->equipe_id = $equipe_id;

        return $this;
    }
}
