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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\System", inversedBy="answers")
     */
    private $system;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Report", inversedBy="answers")
     */
    private $report;

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

    public function getSystem(): ?System
    {
        return $this->system;
    }

    public function setSystem(?System $system): self
    {
        $this->system = $system;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function __toString()
    {
        return $this->getNote();
    }
}
