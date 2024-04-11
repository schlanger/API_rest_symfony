<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Since;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "detailJoueur",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="joueur")
 * )
 *
 * @Hateoas\Relation(
 *       "delete",
 *       href = @Hateoas\Route(
 *           "deleteJoueur",
 *           parameters = { "id" = "expr(object.getId())" },
 *       ),
 *       exclusion = @Hateoas\Exclusion(groups="joueur", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 *
 * @Hateoas\Relation(
 *       "update",
 *       href = @Hateoas\Route(
 *           "updateJoueur",
 *           parameters = { "id" = "expr(object.getId())" },
 *       ),
 *       exclusion = @Hateoas\Exclusion(groups="joueur", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 */

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
class Joueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["joueur", "equipe"])]
    private ?int $id = null;

    #[Groups(["joueur", "equipe"])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(min: 2,max: 15, minMessage: "Le nom doit faire au moins {{limit}} caractères", maxMessage: "Le nom doit faire au plus {{limit}} caractères")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["joueur", "equipe"])]

    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length(min: 2,max: 15, minMessage: "Le prénom doit faire au moins {{limit}} caractères", maxMessage: "Le prénom doit faire au plus {{limit}} caractères")]
    private ?string $prenom = null;

    #[ORM\Column]
    #[Groups(["joueur", "equipe"])]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Groups(["joueur", "equipe"])]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    #[Groups(["joueur", "equipe"])]
    private ?string $poste = null;

    #[ORM\ManyToOne(inversedBy: 'joueurs')]
    #[Groups(["joueur"])]
    private ?Equipe $equipe = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["joueur", "equipe"])]
    #[Since("2.0")]
    private ?int $numero = null;

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

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): static
    {
        $this->equipe = $equipe;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }
}
