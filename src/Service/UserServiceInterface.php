<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * UserServiceInterface class.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginator
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Delete user.
     *
     * @param User $user User
     */
    public function delete(User $user): void;

    /**
     * Can user be deleted?
     *
     * @param User $user User
     *
     * @return bool Can be deleted
     */
    public function canBeDeleted(User $user): bool;

    /**
     * Upgrade password.
     *
     * @param User   $user     User
     * @param string $password Password
     */
    public function upgradePassword(User $user, string $password): void;
}
