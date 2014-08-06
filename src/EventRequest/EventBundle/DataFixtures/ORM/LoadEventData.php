<?php

namespace EventRequest\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use EventRequest\EventBundle\Entity\Event;
use EventRequest\UserBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use EventRequest\UserBundle\Entity\User;

class LoadEventData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    const EVENTS_AMOUNT = 15;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $users = $this->findAllClients();
        $cities = $this->findAllEntities('EventRequestEventBundle:City');

        $amount = self::EVENTS_AMOUNT;

        while ($amount--) {
            $user = $this->getOneOf($users);
            $city = $this->getOneOf($cities);

            $event = new Event();
            $event->setName($this->getRandomString());
            $event->setDescription($this->getRandomString(100, 300));
            $event->setDate(new \DateTime('+'.rand(2, 30).' days'));
            $event->setAddress($this->getRandomString());

            $event->setUser($user);
            $event->setCity($city);

            $manager->persist($event);
            $manager->flush();
        }
    }

    /**
     * @return array[\EventRequest\UserBundle\Entity\User]
     */
    private function findAllClients()
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('doctrine.orm.entity_manager')->getRepository('EventRequestUserBundle:User');

        return $userRepository->findByRole(User::ROLE_CLIENT);
    }

    /**
     * @param $entityType
     * @return mixed
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    private function findAllEntities($entityType)
    {
        $entityRepository = $this->container->get('doctrine.orm.entity_manager')->getRepository($entityType);
        if (!$entityRepository) {
            throw new EntityNotFoundException('Entity repository '.$entityType.' not found');
        }

        return $entityRepository->findAll();
    }

    /**
     * @param array $items
     * @return mixed
     */
    private function getOneOf(array $items)
    {
        $key = array_rand($items, 1);

        return $items[$key];
    }

    /**
     * @param int $minLength
     * @param int $maxLength
     * @return string
     */
    private function getRandomString($minLength = 5, $maxLength = 15)
    {
        $string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam mattis nec augue a egestas. Aenean nisl lorem, porttitor eget scelerisque sed, lacinia sed ipsum. Aliquam ut nisi quam. Mauris sit amet interdum odio. Sed hendrerit fringilla pellentesque. Curabitur pharetra arcu eu aliquet varius. Nunc egestas tempor nibh, eu venenatis ligula fermentum non. Fusce sit amet egestas metus. Suspendisse cursus pharetra rutrum.'.PHP_EOL.PHP_EOL.'Aliquam scelerisque accumsan dignissim. In nec facilisis turpis. Sed et consectetur lorem, sit amet iaculis orci. Mauris mattis nisi feugiat ultricies mollis. Quisque sem elit, tempor ut sodales eget, pharetra eget nunc. Donec ac eleifend eros. Pellentesque mollis mattis interdum. Donec blandit erat sit amet cursus laoreet. Donec consequat orci varius dolor consequat, vel aliquam nisl volutpat. Pellentesque ante enim, tempus dictum enim at, tempus tincidunt lacus. Vestibulum laoreet posuere gravida. Praesent quis libero venenatis, pellentesque metus sit amet, rhoncus sem.';

        $length = rand($minLength, $maxLength);
        $shift = rand(0, 750 - $length);

        return substr($string, $shift, $length);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }
}