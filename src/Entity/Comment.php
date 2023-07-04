<?php
/**
 * Comment entity.
 */

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment.
 */
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{
    /**
     * Primary key.
     *
     * @var int|null Id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Comment.
     *
     * @var string|null Comment
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 500)]
    private ?string $description = null;

    /**
     * Album rating.
     *
     * @var int|null Album rating
     */
    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $rating = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable|null Created at
     */
    #[ORM\Column(type: 'date_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     *
     * @var \DateTimeImmutable|null Updated at
     */
    #[ORM\Column(type: 'date_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Album related to comment.
     *
     * @var Album|null Album
     */
    #[ORM\ManyToOne(targetEntity: Album::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Album $album = null;

    /**
     * Comment author.
     *
     * @var User|null Author
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $author = null;

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for comment content.
     *
     * @return string|null Description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for comment content.
     *
     * @param string $description Description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Getter for comment rating.
     *
     * @return int|null Rating
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * Setter for rating.
     *
     * @param int $rating Rating
     */
    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * Getter for created at.
     *
     * @return \DateTimeImmutable|null Date
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeImmutable $createdAt Date
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for updated at.
     *
     * @return \DateTimeImmutable|null Date
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param \DateTimeImmutable $updatedAt Date
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for album.
     *
     * @return Album|null Album
     */
    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    /**
     * Setter for album.
     *
     * @param Album|null $album Album
     */
    public function setAlbum(?Album $album): void
    {
        $this->album = $album;
    }

    /**
     * Get comment author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set comment author.
     *
     * @param User|null $author Author
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }
}
