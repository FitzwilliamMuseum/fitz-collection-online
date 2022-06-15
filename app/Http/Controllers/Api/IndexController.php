<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    /**
     * [public description]
     * @var string
     */
    public function index(): JsonResponse
    {
        return response()->json(['message' => 'Welcome to the API']);
    }
}
