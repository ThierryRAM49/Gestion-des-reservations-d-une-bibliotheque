<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $autor = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $stock = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'book')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAutor(): ?string
    {
        return $this->autor;
    }

    public function setAutor(string $autor): static
    {
        $this->autor = $autor;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setBook($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getBook() === $this) {
                $reservation->setBook(null);
            }
        }

        return $this;
    }
    // Méthodes de gestion du stock
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function decrementStock(): void
    {
        if ($this->stock <= 0) {
            throw new \LogicException('Stock insuffisant pour ce livre.');
        }

        $this->stock--;
    }

    public function incrementStock(): void
    {
        $this->stock++;
    }
}
