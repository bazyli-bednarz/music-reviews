<?php
/**
 * Album service.
 */

namespace App\Service;

use App\Repository\AlbumRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AlbumService.
 */
class AlbumService
{
    /**
     * Album repository.
     */
    private AlbumRepository $albumRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param AlbumRepository    $albumRepository Album repository
     * @param PaginatorInterface $paginator       Paginator
     */
    public function __construct(AlbumRepository $albumRepository, PaginatorInterface $paginator)
    {
        $this->albumRepository = $albumRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->albumRepository->queryAll(),
            $page,
            AlbumRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }
}
