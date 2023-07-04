<?php
/**
 * Comment repository.
 */

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * Albums per page.
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial comment.{id, description, createdAt, updatedAt, rating}',
                'partial album.{id, title, slug}',
                'partial author.{id, email, username, roles, password, slug, blocked}',
            )
            ->join('comment.album', 'album')
            ->join('comment.author', 'author')
            ->orderBy('comment.updatedAt', 'DESC');
    }

    /**
     * Query comments by album.
     *
     * @param Album $album Album
     *
     * @return QueryBuilder QueryBuilder
     */
    public function queryByAlbum(Album $album): QueryBuilder
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->andWhere('comment.album = :album')
            ->setParameter('album', $album);

        return $queryBuilder;
    }

    /**
     * Count comments by album.
     *
     * @param Album $album Album
     *
     * @return int Number of albums
     *
     * @throws NoResultException        NoResultException
     * @throws NonUniqueResultException NonUniqueResultException
     */
    public function countByAlbum(Album $album): int
    {
        $queryBuilder = $this->getOrCreateQueryBuilder();

        return $queryBuilder->select($queryBuilder->expr()->countDistinct('comment.id'))
            ->where('comment.album = :album')
            ->setParameter(':album', $album)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get average user rating.
     *
     * @param Album $album Album
     *
     * @return float Average album rating
     */
    public function getAverageUserRating(Album $album): float
    {
        $queryBuilder = $this->getOrCreateQueryBuilder();

        try {
            return (float) $queryBuilder->select('AVG(comment.rating) as score')
                ->where('comment.album = :album')
                ->setParameter(':album', $album)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            echo $e;
        }

        return 0;
    }

    /**
     * Save comment.
     *
     * @param Comment $comment Comment
     */
    public function save(Comment $comment): void
    {
        $this->_em->persist($comment);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->_em->remove($comment);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('comment');
    }
}
