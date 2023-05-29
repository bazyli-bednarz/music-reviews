<?php
/**
 * Album service.
 */

namespace App\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Repository\AlbumRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AlbumService.
 */
class AlbumService implements AlbumServiceInterface
{
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
     * @param AlbumRepository    $albumRepository Album repository
     * @param PaginatorInterface $paginator       Paginator
     */
    public function __construct(AlbumRepository $albumRepository, PaginatorInterface $paginator)
    {
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
            $this->albumRepository->queryAll(),
            $page,
            AlbumRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list of albums by category.
     *
     * @param Category $category
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByCategory(Category $category, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->albumRepository->queryByCategory($category),
            $page,
            AlbumRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list of albums by artist.
     *
     * @param Artist $artist
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByArtist(Artist $artist, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->albumRepository->queryByArtist($artist),
            $page,
            AlbumRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Album $album Album entity
     */
    public function save(Album $album): void
    {
        $this->albumRepository->save($album);
    }

    /**
     * Delete entity.
     *
     * @param Album $album Album entity
     */
    public function delete(Album $album): void
    {
        $this->albumRepository->delete($album);
    }
}
