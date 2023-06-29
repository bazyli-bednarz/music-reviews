<?php
/**
 * Album repository.
 */

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AlbumRepository.
 *
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Album>
 */
class AlbumRepository extends ServiceEntityRepository
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
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    /**
     * Query all records.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    public function queryAll(array $filters = []): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial album.{id, title, description, mark, year, createdAt, updatedAt, slug}',
                'partial category.{id, title, slug}',
                'partial tags.{id, title, slug}',
                'partial artists.{id, name, slug}',
                'partial user.{id, email, username, roles, password, slug, blocked}'
            )
            ->join('album.category', 'category')
            ->leftJoin('album.tags', 'tags')
            ->join('album.artists', 'artists')
            ->join('album.author', 'user')
            ->orderBy('album.createdAt', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Query albums by category.
     *
     * @param Category $category
     *
     * @return QueryBuilder
     */
    public function queryByCategory(Category $category): QueryBuilder
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->andWhere('album.category = :category')
            ->setParameter('category', $category);

        return $queryBuilder;
    }

    /**
     * Count albums by category.
     *
     * @param Category $category
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $queryBuilder = $this->getOrCreateQueryBuilder();

        return $queryBuilder->select($queryBuilder->expr()->countDistinct('album.id'))
            ->where('album.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Query albums by artist.
     *
     * @param Artist $artist
     *
     * @return QueryBuilder
     */
    public function queryByArtist(Artist $artist): QueryBuilder
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->andWhere(':artist MEMBER OF album.artists')
            ->setParameter('artist', $artist);

        return $queryBuilder;
    }

    /**
     * Count albums by category.
     *
     * @param Artist $artist
     *
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByArtist(Artist $artist): int
    {
        $queryBuilder = $this->getOrCreateQueryBuilder();

        return $queryBuilder->select($queryBuilder->expr()->countDistinct('album.id'))
            ->where(':artist MEMBER OF album.artists')
            ->setParameter(':artist', $artist)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save album.
     *
     * @param Album $album
     */
    public function save(Album $album): void
    {
        $this->_em->persist($album);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Album $album Album entity
     */
    public function delete(Album $album): void
    {
        $this->_em->remove($album);
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
        return $queryBuilder ?? $this->createQueryBuilder('album');
    }

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder          $queryBuilder Query builder
     * @param array<string, object> $filters      Filters array
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, array $filters = []): QueryBuilder
    {
        if (isset($filters['tag']) && $filters['tag'] instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters['tag']);
        }

        return $queryBuilder;
    }
}
