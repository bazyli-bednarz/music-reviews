<?php
/**
 * Comment service interface.
 */

namespace App\Service;

use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\Artist;
use App\Entity\Category;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list of comments.
     *
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Get paginated list of comments by album.
     *
     * @param Album $album
     * @param int $page
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByAlbum(Album $album, int $page): PaginationInterface;

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
