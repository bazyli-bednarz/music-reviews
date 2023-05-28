<?php
/**
 * Album service interface.
 */

namespace App\Service;

use App\Entity\Category;
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
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByCategory(Category $category, int $page): PaginationInterface;
}
