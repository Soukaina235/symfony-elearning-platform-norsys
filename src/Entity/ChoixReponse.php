<?php

namespace App\Entity;

use App\Repository\ChoixReponseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChoixReponseRepository::class)
 */
class ChoixReponse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $choix;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="choixReponses")
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoix(): ?string
    {
        return $this->choix;
    }

    public function setChoix(string $choix): self
    {
        $this->choix = $choix;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
