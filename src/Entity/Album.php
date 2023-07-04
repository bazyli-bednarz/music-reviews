<?php
/**
 * Album entity.
 */

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Album entity.
 */
#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ORM\Table(name: 'albums')]
class Album
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
     * Album title.
     *
     * @var string|null Album title
     */
    #[ORM\Column(length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    /**
     * Album year of creation.
     *
     * @var int|null Album year of creation
     */
    #[ORM\Column]
    #[Assert\Positive]
    private ?int $year = null;

    /**
     * Album review.
     *
     * @var string|null Album review
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 1500)]
    private ?string $description = null;

    /**
     * Album mark.
     *
     * @var int|null Album mark
     */
    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $mark = null;

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
     * Album category.
     *
     * @var Category|null Categories
     */
    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * Slug.
     *
     * @var string|null Slug
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    /**
     * Tags.
     *
     * @var Collection<int, Tag> Tags
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\JoinTable(name: 'albums_tags')]
    private Collection $tags;

    /**
     * Artists.
     *
     * @var Collection<int, Artist> Artists
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: Artist::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'albums_artists')]
    private Collection $artists;

    /**
     * Album review author.
     *
     * @var User|null User
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $author = null;

    #[ORM\OneToOne(mappedBy: 'album', cascade: ['persist', 'remove'])]
    private ?Cover $cover = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->artists = new ArrayCollection();
    }

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
     * Getter for title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title Title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for year.
     *
     * @return int|null Year
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * Setter for year.
     *
     * @param int $year Year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * Getter for review.
     *
     * @return string|null Description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for review.
     *
     * @param string $description Description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Getter for mark.
     *
     * @return int|null Reviewer's mark
     */
    public function getMark(): ?int
    {
        return $this->mark;
    }

    /**
     * Setter for mark.
     *
     * @param int $mark Reviewer's mark
     */
    public function setMark(int $mark): void
    {
        $this->mark = $mark;
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
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * Getter for slug.
     *
     * @return string|null Slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Setter for slug.
     *
     * @param string $slug Slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * Get tags.
     *
     * @return Collection<int, Tag> Tags
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag.
     *
     * @param Tag $tag Tag
     */
    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * Remove tag.
     *
     * @param Tag $tag Tag entity
     */
    public function removeTag(Tag $tag): void
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get artists.
     *
     * @return Collection<int, Artist> Artists
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    /**
     * Add artist.
     *
     * @param Artist $artist Artist
     */
    public function addArtist(Artist $artist): void
    {
        if (!$this->artists->contains($artist)) {
            $this->artists[] = $artist;
        }
    }

    /**
     * Remove artist.
     *
     * @param Artist $artist Artist
     */
    public function removeArtist(Artist $artist): void
    {
        $this->artists->removeElement($artist);
    }

    /**
     * Getter for author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param User|null $author Author
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    /**
     * Getter for album cover.
     *
     * @return Cover|null Cover
     */
    public function getCover(): ?Cover
    {
        return $this->cover;
    }

    /**
     * Setter for album cover.
     *
     * @param Cover $cover Cover
     */
    public function setCover(Cover $cover): void
    {
        // set the owning side of the relation if necessary
        if ($cover->getAlbum() !== $this) {
            $cover->setAlbum($this);
        }

        $this->cover = $cover;
    }
}
