<?php
/**
 * Album entity.
 */

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ORM\Table(name: 'albums')]
class Album
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Album title.
     */
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * Album year of creation.
     */
    #[ORM\Column]
    private ?int $year = null;

    /**
     * Album review.
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * Album mark.
     */
    #[ORM\Column]
    private ?int $mark = null;

    /**
     * Created at.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * Getter for id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for year.
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * Setter for year.
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * Getter for review.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for review.
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Getter for mark.
     */
    public function getMark(): ?int
    {
        return $this->mark;
    }

    /**
     * Setter for mark.
     */
    public function setMark(int $mark): void
    {
        $this->mark = $mark;
    }

    /**
     * Getter for created at.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for updated at.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * Setter for updated at.
     */
    public function setUpdatedAt(\DateTimeImmutable $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
