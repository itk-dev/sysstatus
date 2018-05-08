<?php

namespace App\Entity;

use App\DBAL\Types\SmileyType;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="SmileyType", nullable=true)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\SmileyType")
     */
    private $smiley;

    public function getId()
    {
        return $this->id;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getSmiley(): ?string
    {
        return $this->smiley;
    }

    public function setSmiley(?string $smiley): self
    {
        $this->smiley = $smiley;

        return $this;
    }

    public function __toString()
    {
        return $this->getId();
    }
}
