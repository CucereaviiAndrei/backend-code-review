<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use App\Message\SendMessage;

// New service introduced for business logic separation
readonly class MessageService
{
    // Constructor injection of dependencies allows the service to handle message dispatching and database interactions.
    public function __construct(
        private MessageRepository $messageRepository,
        private MessageBusInterface $bus,
    ) {
    }

    /**
     * @return Message[] Returns an array of Message objects
     */
    public function getMessagesByStatus(?string $status): array
    {
        return empty($status)
            ? $this->messageRepository->findAll()
            : $this->messageRepository->findByStatus($status);
    }

    public function sendMessage(string $text, int $delay = 0): void
    {
        $this->bus->dispatch(new SendMessage($text), [new DelayStamp($delay)]);
    }

    // Centralized message validation to ensure consistency across different parts of the application.
    public function validateMessage(?string $text): bool
    {
        return !empty($text);
    }
}
