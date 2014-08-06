<?php

namespace EventRequest\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use EventRequest\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    const CLIENTS_AMOUNT = 5;
    const MANAGERS_AMOUNT = 5;
    const PASSWORD = '1234';

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
    public function load(ObjectManager $manager)
    {
        $this->userManager = $this->container->get('fos_user.user_manager');

        $counter = 0;
        while (++$counter <= self::CLIENTS_AMOUNT) {
            $username = 'client'.$counter;

            /** @var \EventRequest\UserBundle\Entity\User $user */
            $user = $this->userManager->createUser();
            $user->setUsername($username);
            $user->setEmail($username.'@example.com');
            $user->setPlainPassword(self::PASSWORD);
            $user->setPhone('+375295080846');
            $user->setEnabled(true);
            $user->addRole(User::ROLE_CLIENT);

            $this->userManager->updateUser($user);
            $manager->persist($user);
        }

        $counter = 0;
        while (++$counter <= self::MANAGERS_AMOUNT) {
            $username = 'manager'.$counter;

            /** @var \EventRequest\UserBundle\Entity\User $user */
            $user = $this->userManager->createUser();
            $user->setUsername($username);
            $user->setEmail($username.'@example.com');
            $user->setPlainPassword(self::PASSWORD);
            $user->setPhone('+375295080846');
            $user->setEnabled(true);
            $user->addRole(User::ROLE_MANAGER);

            $this->userManager->updateUser($user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}