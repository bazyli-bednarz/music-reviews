<?php
/**
 * Album service interface.
 */

namespace App\Service;

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
}
