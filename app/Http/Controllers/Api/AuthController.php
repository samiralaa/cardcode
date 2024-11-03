<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordCodeMail;
use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('AuthToken')->plainTextToken; // Generate token using Sanctum

            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }

        return response()->json([
            'message' => 'Email or Password Invalid',
        ], 401);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'domin_name' => $request->domin_name,
            'password' => Hash::make($request->password),
        ]);

            $card = Card::create([
                'image' => 'add-profile-bigger.jpg',
                'qr_image' => 'image.png',
                'user_id' => $user->id,
            ]);
            $validated['card_id'] = $card->id;
    
        $token = $user->createToken('AuthToken')->plainTextToken; // Generate token using Sanctum

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    public function resetPassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed', // Ensure password confirmation
            'reset_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the reset code is correct and not expired
        if ($user->reset_code !== $request->reset_code || $user->reset_code_expiry < now()) {
            return response()->json(['message' => 'Invalid or expired reset code'], 400);
        }

        // Update the user's password
        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null, // Clear the reset code
            'reset_code_expiry' => null // Clear the expiry time
        ]);

        // Revoke all existing tokens
        $user->tokens()->delete();

        return response()->json(
            ['success' => true],
            200
        );
    }
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        $reset_code = Str::random(6); // Generate a 6 character random code
        //return $reset_code;
        $user->update([
            'reset_code' => $reset_code,
            'reset_code_expiry' => now()->addMinutes(5) // Code expires in 30 minutes
        ]);

        // Send the code to the user's email
        Mail::to($user->email)->send(new ResetPasswordCodeMail($reset_code));

        return response()->json(['message' => 'Reset code sent successfully'], 200);
    }
}
