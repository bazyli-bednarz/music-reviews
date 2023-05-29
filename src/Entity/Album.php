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
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    /**
     * Album year of creation.
     */
    #[ORM\Column]
    #[Assert\Positive]
    private ?int $year = null;

    /**
     * Album review.
     */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 500)]
    private ?string $description = null;

    /**
     * Album mark.
     */
    #[ORM\Column]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $mark = null;

    /**
     * Created at.
     */
    #[ORM\Column(type: 'date_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     */
    #[ORM\Column(type: 'date_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Album category.
     *
     * @var Category|null
     */
    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * Slug.
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    /**
     * Tags.
     *
     * @var Collection<int, Tag>
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\JoinTable(name: 'albums_tags')]
    private Collection $tags;


    /**
     * Artists.
     *
     * @var Collection<int, Artist>
     */
    #[Assert\Valid]
    #[ORM\ManyToMany(targetEntity: Artist::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'albums_artists')]
    private Collection $artists;

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
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for category.
     *
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }


    /**
     * Setter for category.
     *
     * @param Category|null $category
     * @return void
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add tag.
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag): void
    {
        //        if (!$this->tags->contains($tag)) {
        //            $this->tags->add($tag);
        //        }

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
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    /**
     * Add artist.
     *
     * @param Artist $artist
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
     * @param Artist $artist
     */
    public function removeArtist(Artist $artist): void
    {
        $this->artists->removeElement($artist);
    }
}
