<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 * @ORM\Table(name="chats")
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="user_sender_id")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_sender_id", referencedColumnName="id")
     */
    private $userSenderId;

    /**
     * @ORM\Column(type="integer", name="user_recipient_id")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_sender_id", referencedColumnName="id")
     */
    private $userRecipientId;

    /**
     * @ORM\Column(type="string", name="has_unverified_message", columnDefinition="enum('Y', 'N')")
     */
    private $hasUnverifiedMessage;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="deleted_at")
     */
    private $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setUserRecipientId(Int $userSenderId)
    {
        $this->userRecipientId = $userSenderId;
    }

    public function getHasUnverifiedMessage(): ?String
    {
        return $this->hasUnverifiedMessage;
    }
    
    public function setHasUnverifiedMessage(String $hasUnverifiedMessage)
    {
        $this->hasUnverifiedMessage = $hasUnverifiedMessage;
    }

    public function getCreatedAt(): ? DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt()
    {
        $this->createdAt = new DateTime();
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt()
    {
        $this->deletedAt = new DateTime();
    }

    public function getChatData(): Array 
    {
        return [
            'id' => $this->id,
            'userSenderId' => $this->userSenderId,
            'userRecipientId' => $this->userRecipientId,
            'hasUnverifiedMessage' => $this->hasUnverifiedMessage,
            'createdAt' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
        ];
    } 
}
