<?php

namespace App\Controller;

use App\Entity\Chat;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends BaseController
{
    /**
     * @Route("/chats", name="chat")
     */
    public function index(): Response
    {
        $chatRepository = $this->entityManager->getRepository(Chat::class);
        $allChats = $this->getFormattedChats($chatRepository->findAll());
        return new JsonResponse(['chats' => $allChats], 200);
    }

    public function getFormattedChats(Array $userData) {
        $response = [];
        foreach ($userData as $user) {
          if (!$user instanceof Chat) {
            continue;
          }
          array_push($response, $user->getChatData());
        }
    
        return $response;
      }
}
