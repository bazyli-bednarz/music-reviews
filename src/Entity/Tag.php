<?php
/**
 * Tag entity.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tag.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tags')]
#[ORM\UniqueConstraint(name: 'uq_tags_title', columns: ['title'])]
#[UniqueEntity(fields: ['title'])]
class Tag
{
    /**
     * Id.
     *
     * @var int|null Id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Tag title.
     *
     * @var string|null Title
     */
    #[ORM\Column(length: 64)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    private ?string $title = null;

    /**
     * Created at.
     *
     * @var \DateTimeImmutable|null Date
     */
    #[ORM\Column(type: 'date_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     *
     * @var \DateTimeImmutable|null Date
     */
    #[ORM\Column(type: 'date_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Slug.
     *
     * @var string|null Slug
     */
    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\Type('string')]
    #[Assert\Length(max: 64)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    /**
     * Get tag id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get tag title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Tag title.
     *
     * @param string $title Title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get created at.
     *
     * @return \DateTimeImmutable|null Date
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set created at.
     *
     * @param \DateTimeImmutable $createdAt Date
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get updated at.
     *
     * @return \DateTimeImmutable|null Date
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set updated at.
     *
     * @param \DateTimeImmutable $updatedAt Date
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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
}
