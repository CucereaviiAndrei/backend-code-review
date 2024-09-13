<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class MessageController extends AbstractController
{
    public function __construct(private readonly MessageService $messageService)
    {
    }

    #[Route('/messages')]
    public function list(Request $request): JsonResponse
    {
        $status = (string)$request->query->get('status');
        // Obtaining messages by status logic has been moved to MessageService, keeping the controller lean and focused on handling HTTP requests.
        $messages = $this->messageService->getMessagesByStatus($status);

        // Returning a standardized JSON response with HTTP 200 to indicate resource creation.
        return $this->json(['messages' => $messages], Response::HTTP_OK);
    }

    // Now using the POST request body to adhere to REST conventions for data submission.
    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $text = (string)$request->request->get('text');
        // Validating input by delegating to a service method, improving separation of concerns.
        if (!$this->messageService->validateMessage($text)) {
            throw new BadRequestHttpException('Text is required');
        }

        // The message dispatch logic has been moved to MessageService, keeping the controller lean and focused on handling HTTP requests.
        $this->messageService->sendMessage($text);

        // Returning a standardized JSON response with HTTP 201 to indicate resource creation.
        return $this->json(['message' => 'Successfully sent'], Response::HTTP_CREATED);
    }
}
