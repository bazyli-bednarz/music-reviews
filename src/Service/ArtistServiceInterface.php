<?php
/**
 * Artist service interface.
 */

namespace App\Service;

use App\Entity\Artist;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * ArtistServiceInterface.
 */
interface ArtistServiceInterface
{
    /**
     * Get paginated list of artists.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginator
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Artist $artist Artist entity
     */
    public function save(Artist $artist): void;

    /**
     * Delete artist.
     *
     * @param Artist $artist Artist
     */
    public function delete(Artist $artist): void;

    /**
     * Can artist be deleted?
     *
     * @param Artist $artist Artist
     *
     * @return bool Can be deleted
     */
    public function canBeDeleted(Artist $artist): bool;
}
