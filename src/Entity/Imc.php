<?php

namespace App\Entity;

use App\Repository\ImcRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImcRepository::class)
 */
class Imc
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @ORM\Column(type="integer")
     */
    private $imc;

    /**
     * @ORM\Column(type="string")
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getImc(): ?int
    {
        return $this->imc;
    }

    public function setImc(int $imc): self
    {
        $this->imc = $imc;

        return $this;
    }

    public function getDate(): ?String
    {
        return $this->date;
    }

    public function setDate(String $date): self
    {
        $this->date = $date;

        return $this;
    }
}
