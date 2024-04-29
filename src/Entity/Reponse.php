<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 * @ORM\Entity(repositoryClass=App\Repository\ReponseRepository::class)
 * @ORM\Table(name="reponse", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_5FB6DEC72D6BA2D9", columns={"id_reclamation"})})
 * @ORM\Entity
 */
class Reponse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_reponse", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReponse;

    /**
     * @var string
     *
     * @ORM\Column(name="message_rep", type="string", length=255, nullable=false)
     */
    private $messageRep;

    /**
     * @var string
     *
     * @ORM\Column(name="date_rep", type="string", length=255, nullable=false)
     */
    private $dateRep;

    /**
     * @var \Reclamation
     *
     * @ORM\ManyToOne(targetEntity="Reclamation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_reclamation", referencedColumnName="id_reclamation")
     * })
     */
    private $idReclamation;

    public function getIdReponse(): ?int
    {
        return $this->idReponse;
    }

    public function getMessageRep(): ?string
    {
        return $this->messageRep;
    }

    public function setMessageRep(string $messageRep): static
    {
        $this->messageRep = $messageRep;

        return $this;
    }

    public function getDateRep(): ?string
    {
        return $this->dateRep;
    }

    public function setDateRep(string $dateRep): static
    {
        $this->dateRep = $dateRep;

        return $this;
    }

    public function getIdReclamation(): ?Reclamation
    {
        return $this->idReclamation;
    }

    public function setIdReclamation(?Reclamation $idReclamation): static
    {
        $this->idReclamation = $idReclamation;

        return $this;
    }


}
