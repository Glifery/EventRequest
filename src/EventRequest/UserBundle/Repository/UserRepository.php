<?php

namespace EventRequest\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param string $role
     * @return array
     */
    public function findByRole($role)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
        ;

        return $qb->getQuery()->getResult();
    }
}