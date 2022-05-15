<?php

namespace App\Entity;

use App\Repository\CategorieProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieProduitRepository::class)
 */
class CategorieProduit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomCategorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descriptionCategorie;

   
    /**
     * @ORM\OneToMany(targetEntity=ProduitShop::class, mappedBy="categorie")
     */
    private $produitShops;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->produitShops = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId( $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    public function getDescriptionCategorie(): ?string
    {
        return $this->descriptionCategorie;
    }

    public function setDescriptionCategorie(string $descriptionCategorie): self
    {
        $this->descriptionCategorie = $descriptionCategorie;

        return $this;
    }

  

    /**
     * @return Collection<int, ProduitShop>
     */
    public function getProduitShops(): Collection
    {
        return $this->produitShops;
    }

    public function addProduitShop(ProduitShop $produitShop): self
    {
        if (!$this->produitShops->contains($produitShop)) {
            $this->produitShops[] = $produitShop;
            $produitShop->setCategorie($this);
        }

        return $this;
    }

    public function removeProduitShop(ProduitShop $produitShop): self
    {
        if ($this->produitShops->removeElement($produitShop)) {
            // set the owning side to null (unless already changed)
            if ($produitShop->getCategorie() === $this) {
                $produitShop->setCategorie(null);
            }
        }

        return $this;
    }
}
