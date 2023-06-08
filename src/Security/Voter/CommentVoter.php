<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    public const EDIT = 'EDIT';

    /**
     * Delete permission.
     *
     * @const string
     */
    public const DELETE = 'DELETE';

    /**
     * Security.
     *
     * @var Security
     */
    private Security $security;

    /**
     * Contructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);

            case self::DELETE:
                return $this->canDelete($subject, $user);

        }

        return false;
    }

    /**
     * Checks if user can edit comment.
     *
     * @param Comment $comment Comment entity
     * @param User    $user    User
     *
     * @return bool Result
     */
    private function canEdit(Comment $comment, UserInterface $user): bool
    {
        return $comment->getAuthor() === $user;
    }

    /**
     * Checks if user can delete comment.
     *
     * @param Comment $comment Comment entity
     * @param User    $user    User
     *
     * @return bool Result
     */
    private function canDelete(Comment $comment, UserInterface $user): bool
    {
        return $comment->getAuthor() === $user;
    }
}
