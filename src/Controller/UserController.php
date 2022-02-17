<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
  private $entityManager;

  public function __construct($entityManager)
  {
    $this->entityManage = $entityManager;
  }
  public function index() {
    // $manager = $this->entityManager->getManager();
  }
}