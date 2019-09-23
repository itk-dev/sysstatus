<?php


namespace App\Security\Voter;


use App\Entity\Answer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const NEW = 'new';

    protected $authorizationChecker;
    protected $entityManager;

    /**
     * ThemeCategoryVoter constructor.
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $entityManager)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->entityManager = $entityManager;
    }

    /**
     * Is the action supported on the subject?
     *
     * @param string $action
     * @param mixed $subject
     * @param array $classNames
     * @return bool
     */
    protected function isSupported($action, $subject, array $classNames) {
        if (!in_array($action, [self::SHOW, self::EDIT, self::DELETE, self::NEW])) {
            return false;
        }

        if (!in_array(get_class($subject), $classNames)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the user has direct access to the resurse without checking group rights.
     *
     * If it is the creator of the entity or admin/super_admin the user has
     * direct access.
     *
     * @param $subject
     * @param $token
     * @return bool
     */
    protected function userHasAccessDirectAccess($subject, $token) {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }

        $createdBy = $subject->getCreatedBy();
        $createdByUser = $this->entityManager->getRepository(User::class)->findOneBy([
            'username' => $createdBy,
        ]);

        if ($user == $createdByUser) {
            return true;
        }

        return false;
    }
}
