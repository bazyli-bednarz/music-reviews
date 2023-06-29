<?php
/**
 * Category service interface.
 */

namespace App\Service;

use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * CategoryServiceInterface.
 */
interface CategoryServiceInterface
{
    /**
     * Get paginated list of categories.
     *
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page): PaginationInterface;


    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void;

    /**
     * Delete category.
     *
     * @param Category $category
     *
     * @return void
     */
    public function delete(Category $category): void;

    /**
     * Can category be deleted?
     *
     * @param Category $category
     *
     * @return bool
     */
    public function canBeDeleted(Category $category): bool;
}
