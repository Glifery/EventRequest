<?php

namespace EventRequest\EventBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\Doctrine;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EventRequest\EventBundle\Entity\City;
use EventRequest\EventBundle\Entity\Country;

class LoadCityData implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $structure = array(
            'Беларусь' => array(
                'Минск',
                'Гродно',
                'Брест'
            ),
            'Россия' => array(
                'Москва',
                'Санкт-Петербург',
                'Новосибирск'
            ),
            'Украина' => array(
                'Киев',
                'Львов',
                'Одесса'
            )
        );

        foreach ($structure as $countryName => $cities) {
            $country = new Country();
            $country->setName($countryName);
            $manager->persist($country);

            foreach ($cities as $cityName) {
                $city = new City();
                $city->setName($cityName);

                $city->setCountry($country);
                $manager->persist($city);

                $cities[] = $city;
            }
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
        return 2;
    }
}