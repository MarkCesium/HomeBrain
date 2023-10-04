<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IntegratedSystemController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('integrated_system/index.html.twig', [
            'controller_name' => 'IntegratedSystemController',
        ]);
    }

    public function helloWorld(): Response
    {
        return $this->json(['msg' => 'Hello, World!']);
    }
}
