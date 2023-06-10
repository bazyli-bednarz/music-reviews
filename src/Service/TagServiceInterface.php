<?php
/**
 * Tag service interface.
 */

namespace App\Service;

use App\Entity\Tag;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface TagServiceInterface
{
    /**
     * Save entity.
     *
     * @param Tag $tag Tag entity
     */
    public function save(Tag $tag): void;

    /**
     * Delete tag.
     *
     * @param Tag $tag
     *
     * @return void
     */
    public function delete(Tag $tag): void;

    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag;

    /**
     * Find by id.
     *
     * @param string $slug
     *
     * @return Tag|null Tag entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug): ?Tag;
}
