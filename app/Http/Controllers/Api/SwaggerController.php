<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Sportoonline API",
 *     version="1.0.0",
 *     description="API documentation for Sportoonline E-commerce Platform",
 *     @OA\Contact(
 *         email="api@sportoonline.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8888/api/v1",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="https://api.sportoonline.com/v1",
 *     description="Production Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum Authentication"
 * )
 */
class SwaggerController extends Controller
{
    //
}
