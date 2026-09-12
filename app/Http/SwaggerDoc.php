<?php

namespace App\Http;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Mamikos Backend API Documentation",
    description: "API documentation for Mamikos Backend Laravel Project"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\PathItem(
    path: "/api"
)]
class SwaggerDoc
{
}
