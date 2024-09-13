<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $messages = array_map(function () use ($faker) {
            $message = new Message();
            $message->setText($faker->sentence)
                ->setStatus($faker->randomElement(['sent', 'read']));

            return $message;
        }, range(1, 10));

        array_walk($messages, [$manager, 'persist']);

        $manager->flush();
    }
}
