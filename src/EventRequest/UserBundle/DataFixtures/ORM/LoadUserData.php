<?php

namespace EventRequest\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserManager
     */
    private $userManager;

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
    function load(ObjectManager $manager)
    {
        $this->userManager = $this->container->get('fos_user.user_manager');
        $user = $this->userManager->createUser();

        /** @var \EventRequest\UserBundle\Entity\User $user */
        $user->setUsername('admin@example.com');
        $user->setEmail('admin@example.com');
        $user->setPlainPassword('admin');
        $user->addRole('ROLE_SUPER_ADMIN');
        $user->setEnabled(true);
        $user->setPhone('1234567');

        $this->userManager->updateUser($user);
        $manager->persist($user);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 1;
    }
}