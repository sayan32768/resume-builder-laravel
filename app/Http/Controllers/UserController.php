<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

use Illuminate\Support\Facades\Password;

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


    // FORGET PASSWORD
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink([
            'email' => $request->email
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => false,
                'message' => __($status)
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent'
        ]);
    }


    // RESET PASSWORD
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->update([
                    'password' => Hash::make($password),
                ]);

                $user->tokens()->delete(); // logout everywhere
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => __($status)
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }



    // REGISTER
    public function register(Request $request)
    {
        $data = $request->validate([
            'fullName' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password',
            'role' => 'required|in:USER,ADMIN',
        ]);

        $user = User::create([
            'fullName' => $data['fullName'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // 'isVerified' => false,
            'isVerified' => true,
            'role' => $data['role']
        ]);

        // $this->sendVerification($user);

        return response()->json([
            'success' => true,
            // 'message' => 'Verification Email is sent. Please follow the steps mentioned in the email',
            'message' => 'User registered successfully. Please Login using your credentials'
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

        if ($user->is_blocked) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been blocked. Please contact support.',
            ], 403);
        }


        $user->tokens()->delete();


        $accessTokenObj = $user->createToken('access', ['access']);
        $refreshTokenObj = $user->createToken('refresh', ['refresh']);

        $accessToken = $accessTokenObj->plainTextToken;
        $refreshToken = $refreshTokenObj->plainTextToken;

        UserSession::create([
            'user_id' => $user->id,
            'access_token_id' => $accessTokenObj->accessToken->id,
            'refresh_token_id' => $refreshTokenObj->accessToken->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'last_seen_at' => now(),
        ]);


        $user->update([
            'isLoggedIn' => true,
        ]);


        return response()->json([
            'success' => true,
            'message' => "Welcome back {$user->fullName}",
            'data' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'is_admin' => $user->role === 'ADMIN'
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
        UserSession::where('user_id', $request->user()->id)->delete();
        $request->user()->update(['isLoggedIn' => false]);

        return response()->json(['success' => true]);
    }
}
