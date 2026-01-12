<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    // ME
    public function me(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id'       => $user->id,
                'email'    => $user->email,
                'fullName' => $user->fullName,
            ],
        ], 200);
    }



    // REGISTER
    public function register(Request $request)
    {
        $data = $request->validate([
            'fullName' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password',
        ]);

        $user = User::create([
            'fullName' => $data['fullName'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'isVerified' => false,
        ]);

        $this->sendVerification($user);

        return response()->json([
            'success' => true,
            'message' => 'Verification Email is sent. Please follow the steps mentioned in the email',
            // 'data' => $user,
        ], 201);
    }


    // SEND VERIFICATION
    private function sendVerification(User $user)
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // send mail here (Mail::to($user->email)->send(...))
        logger("VERIFY: $url"); // for Postman testing
    }


    // VERIFY EMAIL
    public function verify(Request $request, $id, $hash)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired link'], 403);
        }

        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->email), $hash)) {
            return response()->json(['message' => 'Invalid hash'], 403);
        }

        $user->update(['isVerified' => true]);

        return redirect(env('FRONTEND_URL') . '/verified');
    }


    // LOGIN
    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);


        $user = User::where('email', $data['email'])->first();


        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Password',
            ], 402);
        }

        if (! $user->isVerified) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your account first',
            ], 403);
        }


        $user->tokens()->delete();


        $accessToken = $user->createToken('access', ['access'])->plainTextToken;
        $refreshToken = $user->createToken('refresh', ['refresh'])->plainTextToken;


        $user->update([
            'isLoggedIn' => true,
        ]);


        return response()->json([
            'success' => true,
            'message' => "Welcome back {$user->fullName}",
            'data' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }


    // REFRESH
    public function refresh(Request $request)
    {
        $user = $request->user();

        if (! $request->user()->currentAccessToken()->can('refresh')) {
            return response()->json([
                'message' => 'Invalid refresh token'
            ], 403);
        }

        // delete old access tokens
        $user->tokens()->where('name', 'access')->delete();

        $newAccess = $user->createToken('access', ['access'])->plainTextToken;

        return response()->json([
            'access_token' => $newAccess
        ]);
    }



    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $request->user()->update(['isLoggedIn' => false]);

        return response()->json(['success' => true]);
    }
}
