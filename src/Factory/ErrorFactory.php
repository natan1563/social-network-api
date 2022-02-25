<?php 

namespace App\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorFactory 
{
  public function handlerError(String $message, Int $statusCode): JsonResponse 
  {
    if ($statusCode <= 0 || $statusCode >= 500) {
      $statusCode = 500;
      $message = 'Erro interno no servidor, por favor tente novamente mais tarde!';
    }

    return new JsonResponse(['error' => $message], $statusCode);
  }
}