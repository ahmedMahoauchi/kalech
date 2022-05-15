<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Employe;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
/**
 * Conge
 *
 * @ORM\Table(name="conge", indexes={@ORM\Index(name="IdEmploye", columns={"IdEmploye"})})
 * @ORM\Entity
 */
class Conge
{
    /**
     * @var int
     *
     * @ORM\Column(name="IdConge", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idconge;

    /**
     * @var DateTime
     * @ORM\Column(name="DebutConge", type="date", nullable=false)
     * @Assert\NotBlank(message=" date doit etre non vide")
     */
    private $debutconge;


    /**
     * @var DateTime
     * @ORM\Column(name="FinConge", type="date", nullable=false)
     * @Assert\NotBlank(message=" date doit etre non vide")
     */
    private $finconge;

    /**
     * @Assert\NotBlank(message=" type doit etre non vide")
     *
     * @ORM\Column(type="string", length=255)
     */
    private $typeconge;

    /**
     * @var \Employe
     *
     * @ORM\ManyToOne(targetEntity="Employe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IdEmploye", referencedColumnName="IdEmploye")
     * })
     */
    private $idemploye;

    /**
     * @return int
     */
    public function getIdconge(): ?int
    {
        return $this->idconge;
    }

    /**
     * @param int $idconge
     */
    public function setIdconge(int $idconge): void
    {
        $this->idconge = $idconge;
    }


    public function getDebutconge(): ?\DateTimeInterface
    {
        return $this->debutconge;
    }


    public function setDebutconge(\DateTimeInterface $debutconge): self
    {
        $this->debutconge = $debutconge;
        return $this;
    }

    public function getfinconge(): ?\DateTimeInterface
    {
        return $this->finconge;
    }

    public function setfinconge(\DateTimeInterface $finconge): self
    {
        $this->finconge = $finconge;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeconge(): ?string
    {
        return $this->typeconge;
    }

    /**
     * @param string $typeconge
     */
    public function setTypeconge(string $typeconge): void
    {
        $this->typeconge = $typeconge;
    }


    public function getIdemploye(): ?Employe
    {
        return $this->idemploye;
    }


    public function setIdemploye(?Employe $idemploye): self
    {
        $this->idemploye = $idemploye;
        return  $this;
    }


    public function __toString(){
        return $this->typeconge  ;
}

}




