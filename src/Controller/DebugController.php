<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DebugController extends AbstractController
{
    #[Route('/debug', name: 'app_debug')]
    public function index(): Response
    {
        $info = [
            'php_version' => PHP_VERSION,
            'extensions' => get_loaded_extensions(),
            'working_dir' => getcwd(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'error_reporting' => error_reporting(),
            'display_errors' => ini_get('display_errors'),
        ];
        
        $html = '<h1>Debug Symfony</h1>';
        $html .= '<h2>Informations système :</h2>';
        $html .= '<ul>';
        foreach ($info as $key => $value) {
            if (is_array($value)) {
                $html .= '<li><strong>' . $key . ':</strong> ' . implode(', ', $value) . '</li>';
            } else {
                $html .= '<li><strong>' . $key . ':</strong> ' . $value . '</li>';
            }
        }
        $html .= '</ul>';
        
        return new Response($html, Response::HTTP_OK, ['Content-Type' => 'text/html; charset=utf-8']);
    }
}
