<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SendMessageHandlerTest extends TestCase
{
    public function testInvokePersistsMessage(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Message::class));
        $entityManager->expects($this->once())
            ->method('flush');

        $sendMessage = new SendMessage('Sample message text');

        $handler = new SendMessageHandler($entityManager);
        $handler->__invoke($sendMessage);
    }
}
