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
     * @param \DateTime $from
     * @param \DateTime $to
     * @internal param \DateTime $expired
     * @return array
     */
    public function findByDate(\DateTime $from, \DateTime $to)
    {
        $qb = $this->createQueryBuilder('e');
        $qb
            ->where($qb->expr()->between('e.date', ':date_from', ':date_to'))
            ->setParameter('date_from', $from, \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('date_to', $to, \Doctrine\DBAL\Types\Type::DATETIME)
        ;

        return $qb->getQuery()->getResult();
    }
} 