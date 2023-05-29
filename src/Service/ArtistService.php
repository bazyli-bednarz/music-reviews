<?php
/**
 * Album service.
 */

namespace App\Service;

use App\Entity\Artist;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ArtistService.
 */
class ArtistService implements ArtistServiceInterface
{
    /**
     * Artist repository.
     */
    private ArtistRepository $artistRepository;

    /**
     * Album repository.
     */
    private AlbumRepository $albumRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param ArtistRepository   $artistRepository Artist repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(ArtistRepository $artistRepository, AlbumRepository $albumRepository, PaginatorInterface $paginator)
    {
        $this->artistRepository = $artistRepository;
        $this->albumRepository = $albumRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->artistRepository->queryAll(),
            $page,
            ArtistRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Artist $artist Artist entity
     */
    public function save(Artist $artist): void
    {
        $this->artistRepository->save($artist);
    }

    /**
     * Delete artist.
     *
     * @param Artist $artist
     *
     * @return void
     */
    public function delete(Artist $artist): void
    {
        $this->artistRepository->delete($artist);
    }


    /**
     * Can artist be deleted?
     *
     * @param Artist $artist
     *
     * @return bool
     */
    public function canBeDeleted(Artist $artist): bool
    {
        try {
            $result = $this->albumRepository->countByArtist($artist);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
