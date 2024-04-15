<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "detailEquipe",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="equipe")
 * )
 *
 * @Hateoas\Relation(
 *       "delete",
 *       href = @Hateoas\Route(
 *           "deleteEquipe",
 *           parameters = { "id" = "expr(object.getId())" },
 *       ),
 *       exclusion = @Hateoas\Exclusion(groups="equipe", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 *
 * @Hateoas\Relation(
 *       "update",
 *       href = @Hateoas\Route(
 *           "updateEquipe",
 *           parameters = { "id" = "expr(object.getId())" },
 *       ),
 *       exclusion = @Hateoas\Exclusion(groups="equipe", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 *  )
 */

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
#[ApiResource()]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["equipe", "joueur"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["equipe", "joueur"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(["equipe", "joueur"])]
    private ?string $logo = null;

    #[ORM\Column(length: 255)]
    #[Groups(["equipe", "joueur"])]
    private ?string $surnom = null;

    #[ORM\OneToMany(targetEntity: Joueur::class, mappedBy: 'equipe')]
    #[Groups(["equipe"])]
    private Collection $joueurs;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
    }

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSurnom(): ?string
    {
        return $this->surnom;
    }

    public function setSurnom(string $surnom): static
    {
        $this->surnom = $surnom;

        return $this;
    }

    /**
     * @return Collection<int, Joueur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): static
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
            $joueur->setEquipe($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): static
    {
        if ($this->joueurs->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getEquipe() === $this) {
                $joueur->setEquipe(null);
            }
        }

        return $this;
    }

}
