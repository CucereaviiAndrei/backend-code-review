<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendMessageHandler
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public function __invoke(SendMessage $sendMessage): void
    {
        // Simplified object creation by chaining method calls, improving readability.
        $message = (new Message())
            ->setText($sendMessage->text)
            ->setStatus('sent'); // Status is automatically set to 'sent' during message creation.

        $this->manager->persist($message);
        $this->manager->flush();
    }
}
