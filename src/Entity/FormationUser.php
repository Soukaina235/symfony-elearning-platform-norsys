<?php

namespace App\Entity;

use App\Repository\FormationUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormationUserRepository::class)
 */
class FormationUser
{
    // #[ORM\Id]
    // #[ORM\GeneratedValue]
    // #[ORM\Column]
    // private ?int $id = null;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $formationid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userid;

    /**
     * @ORM\Column(type="float")
     */
    private $avancement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormationid(): ?int
    {
        return $this->formationid;
    }

    public function setFormationid(?int $formationid): self
    {
        $this->formationid = $formationid;

        return $this;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(?int $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getAvancement(): ?int
    {
        return $this->avancement;
    }

    public function setAvancement(?int $avancement): self
    {
        $this->avancement = $avancement;

        return $this;
    }
}
