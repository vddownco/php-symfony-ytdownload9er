<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HealthCheckController extends AbstractController
{
    #[Route('/health', name: 'app_health_check', methods: [Request::METHOD_GET])]
    public function check(string $appVersion): JsonResponse
    {
        $message = [
            'version'   => $appVersion,
            'timestamp' => new \DateTime('now'),
        ];

        return $this->json($message);
    }
}
