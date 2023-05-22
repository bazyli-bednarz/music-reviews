<?php
/**
 * Album service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\AlbumRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    /**
     * Category repository.
     */
    private CategoryRepository $categoryRepository;

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
     * @param CategoryRepository $categoryRepository Category repository
     * @param PaginatorInterface $paginator          Paginator
     */
    public function __construct(CategoryRepository $categoryRepository, AlbumRepository $albumRepository, PaginatorInterface $paginator)
    {
        $this->categoryRepository = $categoryRepository;
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
            $this->categoryRepository->queryAll(),
            $page,
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list of albums by category.
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
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        if (null == $category->getId()) {
            $category->setCreatedAt(new \DateTimeImmutable());
        }
        $category->setUpdatedAt(new \DateTimeImmutable());

        $this->categoryRepository->save($category);
    }

    /**
     * Delete category.
     *
     * @param Category $category
     *
     * @return void
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }


    /**
     * Can category be deleted?
     *
     * @param Category $category
     *
     * @return bool
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->albumRepository->countByCategory($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
