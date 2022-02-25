<?php 

namespace App\Controller;

use App\Entity\User;
use DateTime;
use Exception;
use Doctrine\Persistence\{ManagerRegistry, ObjectManager};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, JsonResponse };

class UserController extends AbstractController
{
  private ObjectManager $entityManager;

  private Array $requiredFields = [
    'username',
    'celphone',
    'name',
    'birthday'
  ];

  public function __construct(ManagerRegistry $entityManager)
  {
    $this->entityManager = $entityManager->getManager();
  }

  public function index() {
    try {
      $allUsers = $this->entityManager->getRepository(User::class)->findAll();
      $response = $this->getFormattedUsers($allUsers);
      return new JsonResponse(['users' => $response]);
    } catch (Exception $e) {
      return new JsonResponse(['error' => 'Falha ao listar os usuários'], 500);
    }
  }

  public function show(Int $id): Response 
  {
    try {
      $user = $this->entityManager->getRepository(User::class)->find($id);

      if (is_null($user)) 
        throw new Exception('Usuário não encontrado', 404);

      return new JsonResponse(['user' => $this->getFormattedUsers([$user])]);
    } catch (Exception $e) {
      $code = ($e->getCode()) ? $e->getCode() : 500;
      return $this->handlerError($e->getMessage(), $code);
    }
  }

  public function create(Request $request): Response
  {
    try {
      $requestBody = json_decode($request->getContent());

      $this->validateProperties($this->requiredFields, $requestBody);

      $userRepository = $this->entityManager->getRepository(User::class);
      $this->usernameVerify($requestBody->username);
      $this->celphoneVerify($requestBody->celphone);

      $user = new User();
      $user->setUsername($requestBody->username);
      $user->setCelphone($requestBody->celphone);
      $user->setName($requestBody->name);
      $user->setBirthDay(new DateTime($requestBody->birthday));

      $this->entityManager->persist($user);
      $this->entityManager->flush();

      $createdUser = $this->getFormattedUsers([$userRepository->findOneBy(['username' => $user->getUsername()])]);
      return new JsonResponse(['user' => $createdUser], 201);
    } catch (Exception $e) {
      return $this->handlerError($e->getMessage(), $e->getCode());
    }
  }

  public function update(Request $request, int $id): Response 
  {
    try {
      $userRepository = $this->entityManager->getRepository(User::class);
      $user = $userRepository->find($id);

      if (!$user) {
        throw new Exception("Usuário não encontrado.", 404);
      }

      $requestBody = json_decode($request->getContent());
      $this->validateProperties($this->requiredFields, $requestBody);
      $this->usernameVerify($requestBody->username, $id);
      $this->celphoneVerify($requestBody->celphone, $id);
      
      $user->setUsername($requestBody->username);
      $user->setCelphone($requestBody->celphone);
      $user->setName($requestBody->name);
      $user->setBirthDay(new DateTime($requestBody->birthday));

      $this->entityManager->flush();
      $createdUser = $this->getFormattedUsers([$userRepository->findOneBy(['username' => $user->getUsername()])]);
      return new JsonResponse(['user' => $createdUser], 200);
    } catch (Exception $e) {
      return $this->handlerError($e->getMessage(), $e->getCode());
    }
  }

  public function getFormattedUsers(Array $userData) {
    $response = [];
    foreach ($userData as $user) {
      if (!$user instanceof User) {
        continue;
      }
      array_push($response, $user->getUserData());
    }

    return $response;
  }

  private function usernameVerify(String $username, Int $id = null)
  {
    $userRepository = $this->entityManager->getRepository(User::class);
    $hasUser = (!is_null($id)) ? $userRepository->find($id) : null;
    $userNameIsAvailable = (is_null($hasUser) || ($hasUser instanceof User) && $hasUser->getUsername() !== $username);

    if ($userRepository->findOneBy(['username' => $username]) && $userNameIsAvailable) {
      throw new Exception('Usuário já cadastrado', 409);
    }

  }

  private function celphoneVerify(String $celphone, Int $id = null)
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

  private function handlerError(String $message, Int $statusCode): JsonResponse 
  {
    if ($statusCode <= 0 || $statusCode >= 500) {
      $statusCode = 500;
      $message = 'Erro interno no servidor, por favor tente novamente mais tarde!';
    }

    return new JsonResponse(['error' => $message], $statusCode);
  }

  private function validateProperties(Array $propertieList, Object $sendedFields) 
  {
    foreach ($propertieList as $propertie) {
      if (!property_exists($sendedFields, $propertie))
        throw new Exception("O campo {$propertie} deve ser preenchido", 400);
      
      if (!strlen($sendedFields->{$propertie}))
        throw new Exception("O campo {$propertie} não pode ficar em branco.", 400);
    }
  }
}