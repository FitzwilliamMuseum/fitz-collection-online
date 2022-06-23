<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/v1/",
 *     description="Home page",
 *     @OA\Response(response="default", description="Welcome page")
 * )
 */
class IndexController extends Controller
{
    /**
     * [public description]
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(['message' => 'Welcome to the API']);
    }
}
