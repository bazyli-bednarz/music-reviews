<?php
/**
 * Tag service.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class TagService.
 */
class TagService implements TagServiceInterface
{
    /**
     * Tag repository.
     */
    private TagRepository $tagRepository;

    /**
     * Constructor.
     *
     * @param TagRepository $tagRepository Tag repository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Save entity.
     *
     * @param Tag $tag Tag entity
     */
    public function save(Tag $tag): void
    {
        $this->tagRepository->save($tag);
    }

    /**
     * Delete tag.
     *
     * @param Tag $tag Tag
     */
    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);
    }

    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title);
    }

    /**
     * Find by id.
     *
     * @param int $id Tag id
     *
     * @return Tag|null Tag entity
     *
     * @throws NonUniqueResultException NonUniqueResultException
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->findOneById($id);
    }

    /**
     * Find by slug.
     *
     * @param string $slug Slug
     *
     * @return Tag|null Tag
     */
    public function findOneBySlug(string $slug): ?Tag
    {
        return $this->tagRepository->findOneBySlug($slug);
    }
}
