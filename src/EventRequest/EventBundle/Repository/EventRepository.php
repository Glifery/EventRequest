<?php
/**
 * Created by PhpStorm.
 * User: Sony
 * Date: 12.08.14
 * Time: 23:21
 */

namespace EventRequest\EventBundle\Repository;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    /**
     * @param \DateTime $expired
     * @return array
     */
    public function findAllNotExpiredEvents(\DateTime $expired)
    {
        $qb = $this->createQueryBuilder('e');
        $qb
            ->where($qb->expr()->gt('e.date', $expired->getTimestamp()))
        ;

        return $qb->getQuery()->getResult();
    }
} 