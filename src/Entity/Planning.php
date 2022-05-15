<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Planning
 *
 * @ORM\Table(name="planning", indexes={@ORM\Index(name="fkPlanning", columns={"idProgramme"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlanningRepository")
 */
class Planning
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPlanning", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idplanning;

    /**
     * @var string
     *@Assert\NotBlank(message="Veuillez remplir le champ 'Nom Planning'")
     * @ORM\Column(name="nomPlanning", type="string", length=255, nullable=false)
     */
    private $nomplanning;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez remplir le champ 'Description'")
     * @Assert\Length(
     *      min = 7,
     *      minMessage = "doit etre >=7 ")
     * @ORM\Column(name="descriptionPlanning", type="text", length=65535, nullable=false)
     */
    private $descriptionplanning;

    /**
     * @var \DateTime
     *@Assert\GreaterThan("today UTC")
     * @ORM\Column(name="datePlanning", type="date", nullable=false)
     */
    private $dateplanning;

    /**
     * @var string
     *@Assert\NotBlank(message="Veuillez remplir le champ 'lieu'")
     * @ORM\Column(name="lieuPlanning", type="string", length=255, nullable=false)
     */
    private $lieuplanning;

    /**
     * @var \Programme
     *@Assert\NotBlank(message="Veuillez remplir le champ 'Programme'")
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProgramme", referencedColumnName="idProgramme")
     * })
     */
    private $idprogramme;

    public function getIdplanning(): ?int
    {
        return $this->idplanning;
    }

    public function getNomplanning(): ?string
    {
        return $this->nomplanning;
    }

    public function setNomplanning(string $nomplanning): self
    {
        $this->nomplanning = $nomplanning;

        return $this;
    }

    public function getDescriptionplanning(): ?string
    {
        return $this->descriptionplanning;
    }

    public function setDescriptionplanning(string $descriptionplanning): self
    {
        $this->descriptionplanning = $descriptionplanning;

        return $this;
    }

    public function getDateplanning(): ?\DateTimeInterface
    {
        return $this->dateplanning;
    }

    public function setDateplanning(\DateTimeInterface $dateplanning): self
    {
        $this->dateplanning = $dateplanning;

        return $this;
    }

    public function getLieuplanning(): ?string
    {
        return $this->lieuplanning;
    }

    public function setLieuplanning(string $lieuplanning): self
    {
        $this->lieuplanning = $lieuplanning;

        return $this;
    }

    public function getIdprogramme(): ?Programme
    {
        return $this->idprogramme;
    }

    public function setIdprogramme(?Programme $idprogramme): self
    {
        $this->idprogramme = $idprogramme;

        return $this;
    }


}
