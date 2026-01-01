<?php

namespace App\Entity;

use App\Repository\QuizQuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuizQuestionRepository::class)]
class QuizQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;

    #[ORM\Column(type: 'text')]
    private ?string $question = null;

    #[ORM\Column(length: 50)]
    private ?string $question_type = 'multiple_choice'; // multiple_choice, true_false, short_answer

    #[ORM\Column(type: 'json', nullable: true)]
    private array $options = [];

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $correct_answer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;
        return $this;
    }

    public function getQuestionType(): ?string
    {
        return $this->question_type;
    }

    public function setQuestionType(string $question_type): static
    {
        $this->question_type = $question_type;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(?array $options): static
    {
        $this->options = $options ?? [];
        return $this;
    }

    public function getCorrectAnswer(): ?string
    {
        return $this->correct_answer;
    }

    public function setCorrectAnswer(?string $correct_answer): static
    {
        $this->correct_answer = $correct_answer;
        return $this;
    }
}
