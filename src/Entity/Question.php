<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
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
    private $question;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $reponse;

    /**
     * @ORM\ManyToOne(targetEntity=Exercice::class, inversedBy="questions")
     */
    private $exercice;

    /**
     * @ORM\OneToMany(targetEntity=ChoixReponse::class, mappedBy="question")
     */
    private $choixReponses;

    public function __construct()
    {
        $this->choixReponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getReponse(): ?int
    {
        return $this->reponse;
    }

    public function setReponse(?int $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): self
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * @return Collection<int, ChoixReponse>
     */
    public function getChoixReponses(): Collection
    {
        return $this->choixReponses;
    }

    public function addChoixReponse(ChoixReponse $choixReponse): self
    {
        if (!$this->choixReponses->contains($choixReponse)) {
            $this->choixReponses[] = $choixReponse;
            $choixReponse->setQuestion($this);
        }

        return $this;
    }

    public function removeChoixReponse(ChoixReponse $choixReponse): self
    {
        if ($this->choixReponses->removeElement($choixReponse)) {
            // set the owning side to null (unless already changed)
            if ($choixReponse->getQuestion() === $this) {
                $choixReponse->setQuestion(null);
            }
        }

        return $this;
    }
}
