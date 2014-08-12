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
        $this->createClients($manager, self::CLIENTS_AMOUNT);
        $this->createManagers($manager, self::CLIENTS_AMOUNT);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param integer $amount
     */
    private function createClients(ObjectManager $manager, $amount)
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass('EventRequest\UserBundle\Entity\Client');
        $userManager = $this->container->get('fos_user.user_manager');

        $counter = 0;
        while (++$counter <= $amount) {
            $username = 'client'.$counter;

            /** @var \EventRequest\UserBundle\Entity\Client $user */
            $user = $userManager->createUser();
            $user->setUsername($username);
            $user->setEmail($username.'@example.com');
            $user->setPlainPassword(self::PASSWORD);
            $user->setPhone('+375295080846');
            $user->setEnabled(true);
            $user->addRole(User::ROLE_CLIENT);

            $userManager->updateUser($user);
            $manager->persist($user);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param integer $amount
     */
    private function createManagers(ObjectManager $manager, $amount)
    {
        $discriminator = $this->container->get('pugx_user.manager.user_discriminator');
        $discriminator->setClass('EventRequest\UserBundle\Entity\Manager');
        $userManager = $this->container->get('fos_user.user_manager');

        $counter = 0;
        while (++$counter <= $amount) {
            $username = 'manager'.$counter;

            /** @var \EventRequest\UserBundle\Entity\Manager $user */
            $user = $userManager->createUser();
            $user->setUsername($username);
            $user->setAddress('manager address...');
            $user->setLicense(1234567890);
            $user->setCompany('manager company...');
            $user->setPlainPassword(self::PASSWORD);
            $user->setEnabled(true);
            $user->addRole(User::ROLE_MANAGER);

            $userManager->updateUser($user);
            $manager->persist($user);
        }
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