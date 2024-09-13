<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    public function testItHasConnection(): void
    {
        self::bootKernel();

        /** @var MessageRepository $messageRepository */
        $messageRepository = self::getContainer()->get(MessageRepository::class);

        $this->assertSame([], $messageRepository->findAll());
    }
}

