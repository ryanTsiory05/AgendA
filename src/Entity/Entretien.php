<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use App\Repository\EntretienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @ORM\Entity(repositoryClass=EntretienRepository::class)
 */
class Entretien
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $heure;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="entretien")
     */
    private $candidat;

    /**
     * @ORM\OneToOne(targetEntity=Calendar::class, mappedBy="entretien", cascade={"persist", "remove"})
     */
    private $calendar;


    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    public function setCalendar(?Calendar $calendar): self
    {
        // unset the owning side of the relation if necessary
        if ($calendar === null && $this->calendar !== null) {
            $this->calendar->setEntretien(null);
        }

        // set the owning side of the relation if necessary
        if ($calendar !== null && $calendar->getEntretien() !== $this) {
            $calendar->setEntretien($this);
        }

        $this->calendar = $calendar;

        return $this;
    }

    public function __toString()
    {
        // $nom = $manager->getCandidat()->getNom();
        return $this->getCandidat()->getNom();
    }
}
