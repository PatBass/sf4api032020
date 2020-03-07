<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PhoneFixtures implements FixtureInterface
{
    private $names = ['iPhone', 'Samsung', 'Huwawei'];
    private $colors = ['black', 'white', 'grey'];
    public function load(ObjectManager $manager)
    {
        for ($i = 1 ; $i < 30 ; $i++) {
            $phone = new Phone();
            $phone->setName($this->names[rand(0,2)].' '.rand(5, 11));
            $phone->setColor($this->colors[rand(0,2)]);
            $phone->setPrice(rand(200,1400));
            $phone->setDescription('Nice phone with more than '.rand(5, 12).' cutting edge features!');

            $manager->persist($phone);
        }

        $manager->flush();
    }
}
