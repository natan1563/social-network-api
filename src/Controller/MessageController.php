<?php

namespace App\Controller;

use App\Entity\Message;
use Exception;
use Symfony\Component\HttpFoundation\{Response, JsonResponse, Request};

class MessageController extends BaseController
{
    private const REQUIRED_FIELDS = [
      'message', 
      'user_sender_id', 
      'user_recipient_id', 
      'chat_id'
    ];

    public function index(Int $chatId): Response
    {
      try {
        $chatRepository = $this->entityManager->getRepository(Message::class);
        $allChats = $this->getFormattedMessages($chatRepository->findBy([
          'chatId' => $chatId 
        ]));
        
        $statusCode = (count($allChats)) ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
        return new JsonResponse(['message' => $allChats], $statusCode);
      } catch (Exception $e) {
        return $this->errorManager->handlerError($e->getMessage(), $e->getCode());
      }
    }

    public function saveMessage(Request $request): Response 
    {
      try {
        $requestBody = json_decode($request->getContent());
        
        $this->validator->validateRequestFormat($requestBody);
        $this->validator->validateProperties(self::REQUIRED_FIELDS, $requestBody);

        $messageEntity = new Message();
        $messageEntity->setMessageData($requestBody);
        
        $this->entityManager->persist($messageEntity);
        $this->entityManager->flush();
        
        return new JsonResponse(['message' => $messageEntity->getMessageData()]);
      } catch (Exception $e) {
        return $this->errorManager->handlerError($e->getMessage(), $e->getCode());
      }
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
