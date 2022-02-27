<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends BaseController
{
    /**
     * @Route("/messages", name="message")
     */
    public function index(): Response
    {
        $chatRepository = $this->entityManager->getRepository(Message::class);
        $allChats = $this->getFormattedMessages($chatRepository->findAll());
        return new JsonResponse(['message' => $allChats], 200);
    }

    public function getFormattedMessages(Array $messageList) {
        $response = [];
        foreach ($messageList as $message) {
          if (!$message instanceof Message) {
            continue;
          }
          array_push($response, $message->getMessageData());
        }
    
        return $response;
    }
}
