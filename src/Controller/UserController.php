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

  public function create(Request $request): Response
  {
    try {
      $requestBody = json_decode($request->getContent());
      $requiredFields = [
        'username',
        'celphone',
        'name',
        'birthday'
      ];

      $this->validateProperties($requiredFields, $requestBody);

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

  private function usernameVerify(String $username)
  {
    $userRepository = $this->entityManager->getRepository(User::class);

    if ($userRepository->findOneBy(['username' => $username]))
      throw new Exception('Usuário já possui cadastro', 409);
  }

  private function celphoneVerify(String $celphone)
  {
    if (strlen($celphone) > 11) 
      throw new Exception('O numero de telefone deve ter no máximo 11 caracteres.', 400);

    $userRepository = $this->entityManager->getRepository(User::class);

    if ($userRepository->findOneBy(['celphone' => $celphone]))
      throw new Exception('Telefone já cadastrado.', 409);
  }

  private function handlerError(String $message, Int $statusCode): JsonResponse 
  {
    if ($statusCode <= 0 || $statusCode > 500) {
      $statusCode = 500;
      $message = 'Erro interno no servidor, por favor tente novamente mais tarde!';
    }

    return new JsonResponse(['error' => $message], $statusCode);
  }

  private function validateProperties(Array $propertieList, Object $sendedFields) 
  {
    foreach ($propertieList as $propertie)
      if (!property_exists($sendedFields, $propertie))
        throw new Exception("O campo {$propertie} deve ser preenchido", 400);
  }
}