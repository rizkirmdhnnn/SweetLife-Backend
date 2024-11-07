<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    // Register
    public function Register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'invalid data',
                'error' => $validate->errors(),
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Mail::to($user->email)->send(new EmailVerification($user));

        return response()->json([
            'status' => true,
            'message' => 'user created',
            'data' => $user,
        ]);
    }


    // Login
    public function Login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'invalid data',
                'error' => $validate->errors(),
            ], 400);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'unauthorized',
                    'error' => 'invalid credentials',
                ], 401);
            }

            // get user
            $user = auth()->user();

            // add custom claims
            $token = JWTAuth::claims([
                'user_id' => $user->id,
            ])->fromUser($user);

            return response()->json([
                'status' => true,
                'message' => 'login success',
                'token' => $token,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'could not create token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Logout
    public function Logout(Request $request)
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json([
                'status' => false,
                'message' => 'unauthorized',
                'error' => 'token not provided',
            ], 400);
        }

        try {
            JWTAuth::parseToken()->invalidate();

            return response()->json([
                'status' => true,
                'message' => 'logout success',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'could not logout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function SendVerificationEmail(User $user)
    {
        // bikin hash
        $hash = hash_hmac('sha256', $user->id . $user->email, env('APP_KEY'));

        // bikin url + hash
        $verificationUrl = url("/api/v1/verify-email/{$user->id}?hash={$hash}");

        // kirim email
        Mail::to($user->email)->send(new EmailVerification($verificationUrl));
    }

    public function VerifyEmail(Request $request, $id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'user not found',
            ], 404);
        }

        if ($user->email_verified_at) {
            return redirect()->route('verify.success');
        }
        // ambil hashnya
        $hash = $request->query('hash');

        // generate ulang hashnya
        $expectedHash = hash_hmac('sha256', $user->id . $user->email, env('APP_KEY'));

        // ojo dibanding bandingke
        if (!hash_equals($expectedHash, $hash)) {
            return view('mail.verify-failed');
        }

        // update usere cik
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('verify.success');
    }
}
