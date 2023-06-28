<?php
/**
 * Album service.
 */

namespace App\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Repository\AlbumRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
     * Comment Repository.
     */
    private CommentRepository $commentRepository;

    /**
     * Tag service.
     */
    private TagServiceInterface $tagService;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param AlbumRepository     $albumRepository   Album repository
     * @param CommentRepository   $commentRepository Comment repository
     * @param TagServiceInterface $tagService        Tag service
     * @param PaginatorInterface  $paginator         Paginator
     */
    public function __construct(AlbumRepository $albumRepository, CommentRepository $commentRepository,
        TagServiceInterface $tagService, PaginatorInterface $paginator)
    {
        $this->albumRepository = $albumRepository;
        $this->commentRepository = $commentRepository;
        $this->tagService = $tagService;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int   $page    Page number
     * @param array $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     *
     * @throws NonUniqueResultException
     */
    public function getPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->albumRepository->queryAll($filters),
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
     * Count comments by album.
     *
     * @param Album $album
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countComments(Album $album): int
    {
        return $this->commentRepository->countByAlbum($album);
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

    /**
     * Prepare filters for the tasks list.
     *
     * @param array $filters
     * @return array
     *
     * @throws NonUniqueResultException
     */
    public function prepareFilters(array $filters): array
    {
        $resultFilters = [];

        if (!empty($filters['tag_slug'])) {
            $tag = $this->tagService->findOneBySlug($filters['tag_slug']);
            if (null !== $tag) {
                $resultFilters['tag'] = $tag;
            }
        }

        return $resultFilters;
    }
}
