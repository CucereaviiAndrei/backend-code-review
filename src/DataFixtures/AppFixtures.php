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

        // The message creation process has been simplified using array_map, making the code cleaner and easier to maintain.
        $messages = array_map(function () use ($faker) {
            $message = new Message();
            $message->setText($faker->sentence);

            /** @var string $status */
            $status = $faker->randomElement(['sent', 'read']);
            $message->setStatus($status);

            return $message;
        }, range(1, 10));

        array_walk($messages, [$manager, 'persist']);

        $manager->flush();
    }
}
