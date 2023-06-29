<?php
/**
 * Album service interface.
 */

namespace App\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * AlbumServiceInterface.
 */
interface AlbumServiceInterface
{
    /**
     * Get paginated list of albums.
     *
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list of albums by category.
     *
     * @param Category $category
     * @param int      $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByCategory(Category $category, int $page): PaginationInterface;

    /**
     * Get paginated list of albums by artist.
     *
     * @param Artist $artist
     * @param int    $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByArtist(Artist $artist, int $page): PaginationInterface;

    /**
     * Count comments by album.
     *
     * @param Album $album
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countComments(Album $album): int;

    /**
     * Save entity.
     *
     * @param Album $album Album entity
     */
    public function save(Album $album): void;

    /**
     * Delete entity.
     *
     * @param Album $album Album entity
     */
    public function delete(Album $album): void;
}
