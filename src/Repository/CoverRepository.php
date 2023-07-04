<?php
/**
 * Cover repository.
 */

namespace App\Repository;

use App\Entity\Cover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cover>
 *
 * @method Cover|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cover|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cover[]    findAll()
 * @method Cover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoverRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cover::class);
    }

    /**
     * Save cover.
     *
     * @param Cover $cover Cover
     */
    public function save(Cover $cover): void
    {
        $this->_em->persist($cover);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Cover $cover Cover entity
     */
    public function delete(Cover $cover): void
    {
        $this->_em->remove($cover);
        $this->_em->flush();
    }
}
