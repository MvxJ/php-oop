<?php

declare(strict_types=1);

namespace App\Entity;

class BugReport extends Entity
{
    private int $id;
    private string $report_type;
    private string $message;
    private string $email;
    private ?string $link;
    private string $created_at;

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function toArray(): array
    {
        return [
            'report_type' => $this->getReportType(),
            'email' => $this->getEmail(),
            'message' => $this->getMessage(),
            'link' => $this->getLink(),
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    public function getReportType(): string
    {
        return $this->report_type;
    }

    public function setReportType(string $report_type): self
    {
        $this->report_type = $report_type;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }
}