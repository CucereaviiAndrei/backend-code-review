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
    private MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    #[Route('/messages')]
    public function list(Request $request): JsonResponse
    {
        $status = (string)$request->query->get('status');
        $messages = $this->messageService->getMessagesByStatus($status);

        return $this->json(['messages' => $messages], Response::HTTP_OK);
    }

    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request): JsonResponse
    {
        $text = (string)$request->request->get('text');
        if (!$this->messageService->validateMessage($text)) {
            throw new BadRequestHttpException('Text is required');
        }

        $this->messageService->sendMessage($text);

        return $this->json(['message' => 'Successfully sent'], Response::HTTP_CREATED);
    }
}
