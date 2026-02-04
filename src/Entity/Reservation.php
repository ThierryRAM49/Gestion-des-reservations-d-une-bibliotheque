<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $user = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateReservation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateReturn = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $book = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDateReservation(): ?\DateTime
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTime $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getDateReturn(): ?\DateTime
    {
        return $this->dateReturn;
    }

    public function setDateReturn(\DateTime $dateReturn): static
    {
        $this->dateReturn = $dateReturn;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getBook(): ?string
    {
        return $this->book;
    }

    public function setBook(string $book): static
    {
        $this->book = $book;

        return $this;
    }
}
