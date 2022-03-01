<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */ 
    private $username;

    /**
     * @ORM\Column(type="string", length=11)
    */
    private $celphone;

    /**
     * @ORM\Column(type="string", length=120)
    */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
    */
    private $birthday;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @var \DateTime
    */ 
    private $created_at;

    public function __construct()
    {
        $this->created_at = new DateTime();    
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string 
    {
        return $this->username;
    }
    
    public function setUsername(String $username) 
    {
        $this->username = $username;
    }

    public function getCelphone(): string 
    {
        return $this->celphone;
    }
    
    public function setCelphone(String $celphone) 
    {
        $this->celphone = $celphone;
    }

    public function getName(): string 
    {
        return $this->name;
    }
    
    public function setName(String $name) 
    {
        $this->name = $name;
    }

    public function getBirthDay(): DateTime
    {
        return $this->birthday;
    }
    
    public function setBirthDay(DateTime $birthday)
    {
        $this->birthday = $birthday;
    }

    public function getCreatedAt(): DateTime 
    {
        return $this->created_at;
    }

    public function getUserData(): array 
    {
        return [
            'id'         => $this->getId(),
            'username'   => $this->getUserName(),
            'celphone'   => $this->getCelphone(),
            'name'       => $this->getName(),
            'birthday'   => $this->getBirthDay(),
            'created_at' => $this->getCreatedAt()
        ];
    }

    public function setUserData($requestBody)
    {
        $this->setUsername($requestBody->username);
        $this->setCelphone($requestBody->celphone);
        $this->setName($requestBody->name);
        $this->setBirthDay(new DateTime($requestBody->birthday));
    }
}
