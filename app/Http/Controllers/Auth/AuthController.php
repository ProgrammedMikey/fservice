<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

use App\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    
      /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     description="Register to Vijilan Security's Lumen system before logging in",
     *     operationId="",
     *     @OA\Parameter(
     *         description="User email",
     *         in="query",
     *         name="email",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="User password",
     *         in="query",
     *         name="password",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Auth is successfull"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Incorrect credentials"
     *     ),
     *     summary="Register to the lumen fresh service integrated system before logging in and getting new token",
     *     tags={
     *         "auth"
     *     }
     * )
     * */


      /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }

     /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    
   /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     description="Login to the system and get new token",
     *     operationId="authUser",
     *     @OA\Parameter(
     *         description="User email",
     *         in="query",
     *         name="email",
     *         required=true,
     *     ),
     *     @OA\Parameter(
     *         description="User password",
     *         in="query",
     *         name="password",
     *         required=true,
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Auth is successfull"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Incorrect credentials"
     *     ),
     *     summary="Login to the lumen fresh service integrated system and get new token",
     *     tags={
     *         "auth"
     *     }
     * )
     * */

    public function login(Request $request)
    {
          //validate incoming request 
          $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

}
