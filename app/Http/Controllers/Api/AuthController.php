<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/api/auth/signup",
 *     tags={"Authorisation"},
 *     summary="Register a new user",
 *     description="Register a new user",
 *     operationId="signup",
 *     @OA\RequestBody(
 *     required=true,
 *     @OA\MediaType(
 *     mediaType="application/json",
 *     @OA\Schema(
 *     type="object",
 *     @OA\Property(
 *     property="name",
 *     description="Name of the user",
 *     type="string",
 *     example="Amanda Huginkiss"
 *     ),
 *     @OA\Property(
 *     property="email",
 *     description="Email of the user",
 *     type="string",
 *     example="amanda@huginkiss.com",
 *     format="email"
 *     ),
 *     @OA\Property(
 *     property="password",
 *     description="Password of the user",
 *     type="string",
 *     example="secret"
 *     ),
 *     ),
 *     ),
 *     ),
 *     @OA\Response(
 *     response=200,
 *     description="Successful operation",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="message",
 *     type="string",
 *     description="User created successfully."
 *     ),
 *     ),
 *     ),
 *     @OA\Response(
 *     response="400",
 *     description="Invalid request",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="User with that email already exists",
 *     ),
 *     ),
 *     ),
 *     @OA\Response(
 *     response="409",
 *     description="Conflict",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="User with that email already exists",
 *     ),
 *     ),
 *     ),
 * ),
 * @OA\Post(
 *     path="/api/auth/login",
 *     tags={"Authorisation"},
 *     summary="Login a user",
 *     description="Login a user: Post a request with username and password, and this will create a new bearer token for you to use to authorise requests",
 *     operationId="login",
 *     @OA\RequestBody(
 *     required=true,
 *     @OA\MediaType(
 *     mediaType="application/json",
 *     @OA\Schema(
 *     type="object",
 *     @OA\Property(
 *     property="email",
 *     description="Email of the user",
 *     type="string",
 *     example="amanda@huginkiss.com",
 *     format="email"
 *     ),
 *     @OA\Property(
 *     property="password",
 *     description="Password of the user",
 *     type="string",
 *     example="secret"
 *     ),
 *     ),
 *     ),
 *     ),
 *     @OA\Response(
 *     response=200,
 *     description="Successful operation",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="access_token",
 *     type="string",
 *     description="Access token",
 *     example="1|sskkskskkskskbdhdj"
 *     ),
 *     @OA\Property(
 *     property="user",
 *     type="object",
 *     description="User",
 *     @OA\Property(
 *     property="id",
 *     type="integer",
 *     description="User id",
 *     example="1"
 *     ),
 *     @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Name",
 *     example="Amanda Huginkiss"
 *     ),
 *     @OA\Property(
 *     property="email",
 *     type="string",
 *     description="Email",
 *     example="amanda@huginkiss.com"
 *     ),
 *     @OA\Property(
 *     property="email_verified_at",
 *     type="string",
 *     description="Email verified at",
 *     example="2022-06-30T08:12:34.000000Z"
 *     ),
 *     @OA\Property(
 *     property="two_factor_secret",
 *     type="string",
 *     description="Two factor secret",
 *     example="NULL"
 *     ),
 *     @OA\Property(
 *     property="two_factor_recovery_codes",
 *     type="string",
 *     description="Two factor recovery codes",
 *     example="NULL"
 *     ),
 *     @OA\Property(
 *     property="two_factor_confirmed_at",
 *     type="string",
 *     description="Two factor recovery codes confirmed at",
 *     example="NULL"
 *     ),
 *     @OA\Property(
 *     property="created_at",
 *     type="string",
 *     description="Created at",
 *     example="2020-06-30T08:12:34.000000Z"
 *     ),
 *     @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     description="Updated at",
 *
 *     ),
 *     ),
 *     ),
 *     ),
 *     @OA\Response(
 *     response="400",
 *     description="Invalid request",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="User with that email already exists",
 *     ),
 *     ),
 *     ),
 * ),
 * @OA\Post(
 *     path="/api/auth/me",
 *     tags={"Authorisation"},
 *     summary="Get your user details without refreshing the token",
 *     description="Retrieve user details",
 *     operationId="me",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *     response=200,
 *     description="Successful operation",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="access_token",
 *     type="string",
 *     description="Access token",
 *     example="1|sskkskskkskskbdhdj"
 *     ),
 *     @OA\Property(
 *     property="user",
 *     type="object",
 *     description="User",
 *     @OA\Property(
 *     property="id",
 *     type="integer",
 *     description="User id",
 *     example="1"
 *     ),
 *     @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Name",
 *     example="Amanda Huginkiss"
 *     ),
 *     @OA\Property(
 *     property="email",
 *     type="string",
 *     description="Email",
 *     example="amanda@huginkiss.com"
 *     ),
 *     @OA\Property(
 *     property="email_verified_at",
 *     type="string",
 *     description="Email verified at",
 *     example="2022-06-30T08:12:34.000000Z"
 *     ),
 *     @OA\Property(
 *     property="two_factor_secret",
 *     type="string",
 *     description="Two factor secret",
 *     example="NULL"
 *     ),
 *     @OA\Property(
 *     property="two_factor_recovery_codes",
 *     type="string",
 *     description="Two factor recovery codes",
 *     example="NULL"
 *     ),
 *     @OA\Property(
 *     property="two_factor_confirmed_at",
 *     type="string",
 *     description="Two factor recovery codes confirmed at",
 *     example="NULL"
 *     ),
 *     @OA\Property(
 *     property="created_at",
 *     type="string",
 *     description="Created at",
 *     example="2020-06-30T08:12:34.000000Z"
 *     ),
 *     @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     description="Updated at",
 *
 *     ),
 *     ),
 *     ),
 *     ),
 *     @OA\Response(
 *     response="400",
 *     description="Invalid request",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="User with that email already exists",
 *     ),
 *     ),
 *     ),
 * ),
 * @OA\Post(
 *     path="/api/auth/logout",
 *     tags={"Authorisation"},
 *     summary="Logout a user",
 *     description="Logout and delete a token",
 *     operationId="logout",
 *     security={{"bearerAuth": {}}},
 *     @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     in="header",
 *     name="Authorization",
 *     ),
 *     @OA\Response(
 *     response=200,
 *     description="Successful operation",
 *     @OA\JsonContent(
 *     type="object",
 *     @OA\Property(
 *     property="User logged out and token deleted",
 *     ),
 *     ),
 *     ),
 * ),
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 400);
        }
        $input = $request->all();
        $user = User::where('email', $input['email'])->first();
        if ($user) {
            return $this->sendError( 'User with that email already exists', [], 409);
        }
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken($input['password'])->plainTextToken;
        $success['name'] = $user->name;
        event(new Registered($user));
        return $this->sendResponse($success, 'User created successfully.');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken($request->email)->plainTextToken
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function me(Request $request): mixed
    {
        return $request->user();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'User logged out and token deleted']);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function revokeTokens(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();
        return response()->json(['message' => 'Tokens revoked']);
    }

}
