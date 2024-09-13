<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored).
     */
    #[Route('/messages')]
    public function list(Request $request, MessageRepository $messageRepository): JsonResponse
    {
        $status = $request->query->get('status');

        $messages = empty($status)
            ? $messageRepository->findAll()
            : $messageRepository->findByStatus((string)$status);

        $formattedMessages = array_map(fn($message) => [
            'uuid' => $message->getUuid(),
            'text' => $message->getText(),
            'status' => $message->getStatus(),
        ], $messages);

        return new JsonResponse(['messages' => $formattedMessages], Response::HTTP_OK);
    }

    #[Route('/messages/send', methods: ['GET'])]
    public function send(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $text = $request->query->get('text');

        if (empty($text)) {
            throw new BadRequestHttpException('Text is required');
        }

        $bus->dispatch(new SendMessage((string)$text));

        return new JsonResponse(['message' => 'Successfully sent'], Response::HTTP_CREATED);
    }
}
