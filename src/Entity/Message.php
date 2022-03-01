<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 * @ORM\Table(name="messages")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", name="message_send_at")
     */
    private $messageSendAt;

    /**
     * @ORM\Column(type="integer", name="user_sender_id")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_sender_id", referencedColumnName="id")
     */
    private $userSenderId;

    /**
     * @ORM\Column(type="integer", name="user_recipient_id")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_recipient_id", referencedColumnName="id")
     */
    private $userRecipientId;

    /**
     * @ORM\Column(type="integer", name="chat_id")
     * @ORM\ManyToOne(targetEntity="Chat")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     */
    private $chatId;
 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?String
    {
        return $this->message;
    }

    public function setMessage(String $message)
    {
        $this->message = $message;
    }

    public function getMessageSendAt(): ?DateTime
    {
        return $this->messageSendAt;
    }

    public function setMessageSendAt()
    {
        $this->messageSendAt = new DateTime();
    }

    public function getUserSenderId(): ?int
    {
        return $this->userSenderId;
    }
    
    public function setUserSenderId(Int $userSenderId)
    {
        $this->userSenderId = $userSenderId;
    }

    public function getUserRecipientId(): ?int
    {
        return $this->userRecipientId;
    }

    public function setUserRecipientId(Int $userRecipientId)
    {
        $this->userRecipientId = $userRecipientId;
    }

    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    public function setChatId(Int $chatId)
    {
        $this->chatId = $chatId;
    }

    public function getMessageData(): ?Array 
    {
        return [
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'messageSendAt' => $this->getMessageSendAt(),
            'userSenderId' => $this->getUserSenderId(),
            'userRecipientId' => $this->getUserRecipientId(),
            'chatId' => $this->getChatId(),
        ];
    }

    public function setMessageData(Object $messageData)
    {
        $this->setMessage($messageData->message);
        $this->setMessageSendAt(new DateTime());
        $this->setUserSenderId($messageData->user_sender_id);
        $this->setUserRecipientId($messageData->user_recipient_id);
        $this->setChatId($messageData->chat_id);
    }
}
