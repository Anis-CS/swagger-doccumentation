<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use function Nette\Utils\data;


/**
 *  @OA\info(
 *      title="Laravel 11 Passport Apis (Documentation)",
 *      version="1.0.0"
 * )
 */
class ApiController extends Controller
{
    // New User Register
    // POST[ Name, Email, Password]
/**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Register"},
     *     summary="Register a new user",
     *     description="Creates a new user in the system with the provided details, including a profile picture upload.",
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "password_confirmation"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="securePassword123"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password", example="securePassword123")
     *             )
     *         ),
     *             @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "password_confirmation"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="securePassword123"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password", example="securePassword123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             ),
     *             @OA\Property(property="message", type="string", example="User registered successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="The email has already been taken.")
     *                 ),
     *                 @OA\Property(property="password", type="array",
     *                     @OA\Items(type="string", example="The password must be at least 8 characters.")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Validation failed.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while registering the user.")
     *         )
     *     )
     * )
*/
    public function register(Request $request){
        //Request validation
        $request->validate([
            "name" => "required | string",
            "email" =>  "required | string | email | unique:users",
            "password" => "required | confirmed",
        ]);
        //User Create
        User::Create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        return response()->json([
            "status"=> true,
            "message"=> "User Register Successfully.",
            "data"=> [],
        ]);
    }
    // User Login Form
    // POST[ Email, Password]
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"login"},
     *     summary="Login a user",
     *     description="Allows a user to log in with their email and password.",
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="securePassword123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="access_token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             ),
     *             @OA\Property(property="message", type="string", example="User logged in successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Incorrect credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid email or password.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred while trying to log in.")
     *         )
     *     )
     * )
     */
    public function login(Request $request){
        // Request Validation
        $request->validate([
            "email" => "required | email | string",
            "password" => "required"
        ]);

        // User login
        $user = User::where('email', $request->email)->first();
        if (!empty($user)){
            // user exists
            if (Hash::check($request->password, $user->password)){
                // Password Match
                $token = $user->createToken('mytoken')->accessToken;
                //create token
                return response()->json([
                    "status"=> true,
                    "message"=> "User Login Successfully.",
                    "token" => $token,
                    "data"  => [],
                ]);
            }else{
                return response()->json([
                    "status"=> false,
                    "message"=> "Your Password didn't Match.",
                    "data"  => [],
                ]);
            }
        }else{
            return response()->json([
                "status" =>false,
                "message" => "Invalid Email Address.",
                "data" => []
            ]);
        }
    }
    // User Profile
    // GET[ Auth: Token ]
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="User profile",
     *     description="Profile information.",
     *     operationId="profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile Information",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     )
     * )
     */
    public function profile(){
        $userData = auth()->user();
        return response()->json([
            "status"=>true,
            "message"=>"Profile Information",
            "data"=>$userData
        ]);
    }
    // User logout
    // GET[ Auth: Token ]
    /**
     * @OA\Get(
     *     path="/api/logout",
     *     tags={"logout"},
     *     summary="Logout user",
     *     description="Logs out the authenticated user by invalidating the current access token.",
     *     operationId="logoutUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred during logout.")
     *         )
     *     )
     * )
     */
    public function logout(){
        $token = auth()->user()->token();
        $token->revoke();
        return response()->json([
            "status"=>true,
            "message"=>"Your Profile logout successfully."
        ]);
    }
}
