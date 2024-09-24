<?php

namespace App\Entity;

use App\DBAL\Types\SmileyType;
use App\Repository\AnswerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation\Versioned;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
#[\Gedmo\Mapping\Annotation\Loggable]
class Answer
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Versioned]
    private ?string $note = null;


    #[ORM\Column(type: 'SmileyType' , nullable: true)]
    #[DoctrineAssert\EnumType(entity: SmileyType::class)]
    #[Versioned]
    private ?string $smiley = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    private ?System $system = null;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    private ?Report $report = null;

    public function __toString()
    {
        return $this->getNote() ?: 'No note';
    }

    public function getId(): ?int
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

    public function setSmiley(string $smiley): self
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
}
