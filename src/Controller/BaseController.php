<?php

namespace App\Controller;

use App\Factory\{ErrorFactory, ValidatorFactory};
use Doctrine\Persistence\{ManagerRegistry, ObjectManager};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    protected ObjectManager $entityManager;
    protected ValidatorFactory $validator;
    protected ErrorFactory $errorManager;

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
}
