<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\MessageRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use App\Message\SendMessage;

readonly class MessageService
{

    public function __construct(
        private MessageRepository $messageRepository,
        private MessageBusInterface $bus,
    ) {
    }

    public function getMessagesByStatus(?string $status): array
    {
        return empty($status) ? $this->messageRepository->findAll() : $this->messageRepository->findByStatus($status);
    }

    public function sendMessage(string $text, int $delay = 0): void
    {
        $this->bus->dispatch(new SendMessage($text), [new DelayStamp($delay)]);
    }

    public function validateMessage(?string $text): bool
    {
        return !empty($text);
    }
}
