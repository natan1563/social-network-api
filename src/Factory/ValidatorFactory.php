<?php 

namespace App\Factory;

use App\Entity\User;
use Doctrine\Persistence\{ManagerRegistry, ObjectManager};
use Exception;

class ValidatorFactory
{
  private ObjectManager $entityManager;
  
  public function __construct(
    ManagerRegistry $entityManager
  )
  {
   $this->entityManager = $entityManager->getManager();
  }

  public function usernameVerify(String $username, Int $id = null)
  {
    $userRepository = $this->entityManager->getRepository(User::class);
    $hasUser = (!is_null($id)) ? $userRepository->find($id) : null;
    $userNameIsAvailable = (is_null($hasUser) || ($hasUser instanceof User) && $hasUser->getUsername() !== $username);

    if ($userRepository->findOneBy(['username' => $username]) && $userNameIsAvailable) {
      throw new Exception('Usuário já cadastrado', 409);
    }

  }

  public function celphoneVerify(String $celphone, Int $id = null)
  {
    if (strlen($celphone) > 11) 
      throw new Exception('O numero de telefone deve ter no máximo 11 caracteres.', 400);

    if (!is_numeric($celphone))
      throw new Exception('O telefone deve conter apenas numeros.', 400);

    $userRepository = $this->entityManager->getRepository(User::class);
    $hasUser = (!is_null($id)) ? $userRepository->find($id) : null;
    $celphoneIsAvailable = (is_null($hasUser) || ($hasUser instanceof User) && $hasUser->getCelphone() !== $celphone);

    if ($userRepository->findOneBy(['celphone' => $celphone]) &&  $celphoneIsAvailable)
      throw new Exception('Telefone já cadastrado.', 409);
  }
  
  public function validateProperties(Array $propertieList, Object $sendedFields) 
  {
    foreach ($propertieList as $propertie) {
      if (!property_exists($sendedFields, $propertie))
        throw new Exception("O campo {$propertie} deve ser preenchido", 400);
      
      if (!strlen($sendedFields->{$propertie}))
        throw new Exception("O campo {$propertie} não pode ficar em branco.", 400);
    }
  }

  public function validateRequestFormat(Object $requestBody)
  {
    if (is_null($requestBody))
      throw new Exception("Ops! Parece que algo foi enviado de forma errada, tente novamente!", 400);
  }
}