<?php
/**
 * User repository.
 */
namespace App\Repository;

use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find users who are not admins.
     *
     * @return User[] Returns an array of User objects
     */
    public function findNonAdminUsers()
    {
        $users = $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        return array_filter($users, function (User $user) {
            return !in_array('ROLE_ADMIN', $user->getRoles());
        });
    }
}
