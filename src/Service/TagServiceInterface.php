<?php
/**
 * Tag service interface.
 */

namespace App\Service;

use App\Entity\Tag;
use Doctrine\ORM\NonUniqueResultException;

/**
 * TagServiceInterface class.
 */
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
     * @param Tag $tag Tag
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
     * @param string $slug Slug
     *
     * @return Tag|null Tag entity
     *
     * @throws NonUniqueResultException NonUniqueResultException
     */
    public function findOneBySlug(string $slug): ?Tag;
}
