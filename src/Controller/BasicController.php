<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BasicController
{
    #[Route('/basic', name: 'app_basic')]
    public function index(): Response
    {
        return new Response(
            '<h1>Test Basique</h1><p>Si vous voyez ceci, Symfony fonctionne !</p>',
            Response::HTTP_OK,
            ['Content-Type' => 'text/html; charset=utf-8']
        );
    }
}
