<?php

namespace App\Entity;

use App\Repository\RessourceUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RessourceUserRepository::class)
 */
class RessourceUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ressourceid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRessourceid(): ?int
    {
        return $this->ressourceid;
    }

    public function setRessourceid(?int $ressourceid): self
    {
        $this->ressourceid = $ressourceid;

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
}
