<?php
/**
 * Cover entity.
 */

namespace App\Entity;

use App\Repository\CoverRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cover class.
 */
#[ORM\Entity(repositoryClass: CoverRepository::class)]
#[ORM\Table(name: 'covers')]
#[ORM\UniqueConstraint(name: 'uq_covers_filename', columns: ['filename'])]
class Cover
{
    /**
     * Primary key.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Album.
     *
     * @var Album|null
     */
    #[ORM\OneToOne(inversedBy: 'cover', targetEntity: Album::class, cascade: ['persist', 'remove'], fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Type(Album::class)]
    private ?Album $album = null;

    /**
     * Filename.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 191)]
    #[Assert\Type('string')]
    private ?string $filename = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * Getter for filename.
     *
     * @return string|null Filename
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }


    /**
     * Setter for filename.
     *
     * @param string|null $filename Filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }
}
