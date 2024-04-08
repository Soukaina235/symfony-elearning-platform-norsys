<?php

namespace App\Entity;

use App\Repository\ExerciceUserRepository;
use Doctrine\ORM\Mapping as ORM;

// #[ORM\Entity(repositoryClass: ExerciceUserRepository::class)]
/**
 * @ORM\Entity(repositoryClass=ExerciceUserRepository::class)
 */
class ExerciceUser
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
    private $exerciceid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $userid;

    /**
     * @ORM\Column(type="float")
     */
    private $score;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExerciceid(): ?int
    {
        return $this->exerciceid;
    }

    public function setExerciceid(?int $exerciceid): self
    {
        $this->exerciceid = $exerciceid;

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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }
}
