<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/v1/hello')]
class HelloWorldController extends AbstractController
{
    public function __construct()
    {
        
    }

    #[Route('', methods: ['GET'])]
    public function getHelloWorld(): JsonResponse
    {
        return new JsonResponse('Hello World!');
    }
}
