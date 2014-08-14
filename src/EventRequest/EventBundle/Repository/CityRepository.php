<?php

namespace EventRequest\EventBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
    /**
     * @param string $country_id
     * @return array
     */
    public function findByCountryId($country_id)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where($qb->expr()->eq('c.country', ':country'))
            ->setParameter('country', $country_id)
        ;

        return $qb->getQuery()->getArrayResult();
    }
}