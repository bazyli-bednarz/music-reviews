<?php
/**
 * Comment service interface.
 */

namespace App\Service;

use App\Entity\Album;
use App\Entity\Comment;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list of comments.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginator
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list of comments by album.
     *
     * @param Album $album Album
     * @param int   $page  Page number
     *
     * @return PaginationInterface Paginator
     */
    public function getPaginatedListByAlbum(Album $album, int $page): PaginationInterface;

    /**
     * Count comments in album.
     *
     * @param Album $album Album
     *
     * @return int Number of albums
     *
     * @throws NoResultException        NoResultException
     * @throws NonUniqueResultException NonUniqueResultException
     */
    public function countByAlbum(Album $album): int;

    /**
     * Average album user rating.
     *
     * @param Album $album Albun
     *
     * @return float Album rating
     */
    public function getAverageUserRating(Album $album): float;

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void;

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void;
}
