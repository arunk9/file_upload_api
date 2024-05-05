<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        //
    }

    public function getUsers($batchId, $filter = "", $orderBy = 'username', $order = 'ASC')
    {
        $queryBuilder = $this->createQueryBuilder('p')
                              ->select('p.username, p.email');

        if ($filter) {
            $queryBuilder->where("p.username LIKE '%$filter%'")
                         ->orWhere("p.email LIKE '%$filter%'");
        }

        $queryBuilder
                     ->andwhere("p.batch_id = '$batchId'")
                     ->orderBy("p.$orderBy", $order);

        return $queryBuilder->getQuery()->execute();
    }
}
