<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface UserServiceInterface
{
    /**
     * Get paginated list of users.
     *
     * @param int $page
     *
     * @return PaginationInterface
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
     * @param User $user
     *
     * @return void
     */
    public function delete(User $user): void;

    /**
     * Can user be deleted?
     *
     * @param User $user
     *
     * @return bool
     */
    public function canBeDeleted(User $user): bool;

    /**
     * Upgrade password.
     *
     * @param User $user User entity
     */
    public function upgradePassword(User $user, string $password): void;
}
