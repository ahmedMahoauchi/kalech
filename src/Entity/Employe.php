<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Employe
 *
 * @ORM\Table(name="employe")
 * @ORM\Entity
 */
class Employe
{
    /**
     * @var int
     *
     * @ORM\Column(name="IdEmploye", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $idemploye;


    /**
     * @Assert\NotBlank(message=" nom doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un nom au mini de 3 caracteres"
     *
     *     )
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $nomemploye;



    /**
     * @Assert\NotBlank(message=" date doit etre non vide")
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $dateemploye;



    /**
     * @Assert\NotBlank(message=" numero doit etre non vide")
     * @ORM\Column(type="integer", length=255)
     * @Groups("post:read")
     */
    private $numemploye;

    /**
     * @Assert\NotBlank(message=" salaire doit etre non vide")
     * @ORM\Column(type="integer", length=255)
     * @Groups("post:read")
     */
    private $salaireemploye;

    /**
     * @return int
     */
    public function getIdemploye(): int
    {
        return $this->idemploye;
    }

    /**
     * @param int $idemploye
     */
    public function setIdemploye(int $idemploye): void
    {
        $this->idemploye = $idemploye;
    }

    /**
     * @return string
     */
    public function getNomemploye(): ?string
    {
        return $this->nomemploye;
    }

    /**
     * @param string $nomemploye
     */
    public function setNomemploye(string $nomemploye): void
    {
        $this->nomemploye = $nomemploye;
    }

    /**
     * @return string
     */
    public function getDateemploye(): ?string
    {
        return $this->dateemploye;
    }

    /**
     * @param string $dateemploye
     */
    public function setDateemploye(string $dateemploye): void
    {
        $this->dateemploye = $dateemploye;
    }

    /**
     * @return int
     */
    public function getNumemploye(): ?int
    {
        return $this->numemploye;
    }

    /**
     * @param int $numemploye
     */
    public function setNumemploye(int $numemploye): void
    {
        $this->numemploye = $numemploye;
    }

    /**
     * @return int
     */
    public function getSalaireemploye(): ?int
    {
        return $this->salaireemploye;
    }

    /**
     * @param int $salaireemploye
     */
    public function setSalaireemploye(int $salaireemploye): void
    {
        $this->salaireemploye = $salaireemploye;
    }

    public function __toString()
    {
        return $this->nomemploye;
    }


}
