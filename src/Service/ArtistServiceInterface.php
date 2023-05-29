<?php
/**
 * Artist service interface.
 */

namespace App\Service;

use App\Entity\Artist;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface ArtistServiceInterface
{
    /**
     * Get paginated list of artists.
     *
     * @param int $page
     *
     * @return PaginationInterface
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
     * @param Artist $artist
     *
     * @return void
     */
    public function delete(Artist $artist): void;

    /**
     * Can artist be deleted?
     *
     * @param Artist $artist
     *
     * @return bool
     */
    public function canBeDeleted(Artist $artist): bool;
}
