<?php 

namespace App\Controller;

use App\Entity\User;
use App\Factory\{ValidatorFactory, ErrorFactory};
use DateTime;
use Exception;
use Doctrine\Persistence\{ManagerRegistry, ObjectManager};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{ Request, Response, JsonResponse };

class UserController extends AbstractController
{
  private ObjectManager $entityManager;
  private ValidatorFactory $validator;
  private ErrorFactory $errorManager;

  private Array $requiredFields = [
    'username',
    'celphone',
    'name',
    'birthday'
  ];

  public function __construct(
    ManagerRegistry $entityManager,
    ValidatorFactory $validator,
    ErrorFactory $error
    )
  {
    $this->entityManager = $entityManager->getManager();
    $this->validator = $validator;
    $this->errorManager = $error;
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
      return $this->errorManager->handlerError($e->getMessage(), $code);
    }
  }

  public function create(Request $request): Response
  {
    try {
      $requestBody = json_decode($request->getContent());

      $this->validator->validateProperties($this->requiredFields, $requestBody);

      $userRepository = $this->entityManager->getRepository(User::class);
      $this->validator->usernameVerify($requestBody->username);
      $this->validator->celphoneVerify($requestBody->celphone);

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
      return $this->errorManager->handlerError($e->getMessage(), $e->getCode());
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
      $this->validator->validateProperties($this->requiredFields, $requestBody);
      $this->validator->usernameVerify($requestBody->username, $id);
      $this->validator->celphoneVerify($requestBody->celphone, $id);
      
      $user->setUsername($requestBody->username);
      $user->setCelphone($requestBody->celphone);
      $user->setName($requestBody->name);
      $user->setBirthDay(new DateTime($requestBody->birthday));

      $this->entityManager->flush();
      $createdUser = $this->getFormattedUsers([$userRepository->findOneBy(['username' => $user->getUsername()])]);
      return new JsonResponse(['user' => $createdUser], 200);
    } catch (Exception $e) {
      return $this->errorManager->handlerError($e->getMessage(), $e->getCode());
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
}