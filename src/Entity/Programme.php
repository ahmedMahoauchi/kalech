<?php

namespace App\Entity;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Programme
 *
 * @ORM\Table(name="programme", indexes={@ORM\Index(name="fkprogramme", columns={"idUser"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProgrammeRepository")
 */
class Programme
{
    /**
     * @var int
     *
     * @ORM\Column(name="idProgramme", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idprogramme;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez remplir le champ 'Nom Programme'")
     * @ORM\Column(name="nomProgramme", type="string", length=255, nullable=false)
     */
    private $nomprogramme;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez remplir le champ 'Description'")
     * @Assert\Length(
     *      min = 7,
     *      minMessage = "doit etre >=7 ")
     * @ORM\Column(name="descriptionProgramme", type="text", length=65535, nullable=false)
     */
    private $descriptionprogramme;

    /**
     * @var int
     *@Assert\NotBlank(message="Veuillez remplir le champ 'Niveau Programme'")
     * @ORM\Column(name="niveauProgramme", type="integer", nullable=false)
     */
    private $niveauprogramme;

    /**
     * @var int
     *@Assert\NotBlank(message="Veuillez remplir le champ 'Genre Programme'")
     * @ORM\Column(name="genreProgramme", type="integer", nullable=false)
     */
    private $genreprogramme;

    /**
     * @var string
     *@Assert\NotBlank(message="Veuillez remplir le champ 'Type Programme'")
     * @ORM\Column(name="typeProgramme", type="string", length=255, nullable=false)
     */
    private $typeprogramme;

    /**
     * @var string

     * @ORM\Column(name="imageProgramme", type="string", length=255, nullable=false)
     */
    private $imageprogramme;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $iduser;

    public function getIdprogramme(): ?int
    {
        return $this->idprogramme;
    }

    public function getNomprogramme(): ?string
    {
        return $this->nomprogramme;
    }

    public function setNomprogramme(string $nomprogramme): self
    {
        $this->nomprogramme = $nomprogramme;

        return $this;
    }

    public function getDescriptionprogramme(): ?string
    {
        return $this->descriptionprogramme;
    }

    public function setDescriptionprogramme(string $descriptionprogramme): self
    {
        $this->descriptionprogramme = $descriptionprogramme;

        return $this;
    }

    public function getNiveauprogramme(): ?int
    {
        return $this->niveauprogramme;
    }

    public function setNiveauprogramme(int $niveauprogramme): self
    {
        $this->niveauprogramme = $niveauprogramme;

        return $this;
    }

    public function getGenreprogramme(): ?int
    {
        return $this->genreprogramme;
    }

    public function setGenreprogramme(int $genreprogramme): self
    {
        $this->genreprogramme = $genreprogramme;

        return $this;
    }

    public function getTypeprogramme(): ?string
    {
        return $this->typeprogramme;
    }

    public function setTypeprogramme(string $typeprogramme): self
    {
        $this->typeprogramme = $typeprogramme;

        return $this;
    }

    public function getImageprogramme(): ?string
    {
        return $this->imageprogramme;
    }

    public function setImageprogramme(string $imageprogramme): self
    {
        $this->imageprogramme = $imageprogramme;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }


}
