<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth; 

class AuthController extends Controller
{
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'logout']]);
    }

    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $email = $request->get('email');
            if (User::where('email', $email)->count() > 0) {
                $msg = 'There is already an account with this email registered in the system. And it is not possible to create another one.';
                return response()->json(['error' => 'login error', 'message' => $msg], 401);
            }
            $v = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password'  => 'required|min:3|confirmed',
            ]);
            if ($v->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $v->errors(),
                ], 422);
            }
            $params = $request->all();
            $params['password'] = bcrypt($request->password);
            $user = User::create($params);
            DB::commit();
            $credentials = $request->only('email', 'password');
            $token = Auth::guard('api')->attempt($credentials);
            return response()->json(['success' => true, 'token' => $token], 200);
        } catch (Exception $e) {
            DB::rollback();
            $errorData = [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
                'dados' => $request->all(),
              ];
              Log::channel("auth")->error("Erro register", $errorData);
              $responseData = [
                  'status' => false,
                  'mensage' => 'An error occurred while registering, please check your details and try again.',
                  'error' => $errorData,
              ];
            return response()->json(['success' => false, 'responseData' => $responseData], 500);
        }
    }

    public function login(Request $request)
    {
        try {
                $credentials = $request->only('email', 'password');
                $checkUserExists = User::where('email', $request->get('email'))->first();
                if (!$checkUserExists) {
                    return response()->json(['status' => 'error', 'message' => 'Email not found'], 422);
                }
                if (Auth::attempt($credentials)) {
                    $this->guard()->attempt($credentials);
                    $token = Auth::guard('api')->attempt($credentials);
                    return response()->json(['status' => 'success', 'check' => Auth::check(), 'token' => $token], 200)->header('Authorization', $token);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
                }
        } catch (Exception $e) {
            $errorData = [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
                'dados' => $request->all(),
              ];
              Log::channel("auth")->error("Erro Login", $errorData);
              $responseData = [
                  'status' => false,
                  'mensage' => 'Incorrect data or non-existent user. Try again later.',
                  'error' => $errorData,
              ];
            return response()->json(['success' => false, 'responseData' => $responseData], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (Exception $e) {
            $errorData = [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
              ];
              Log::channel("auth")->error("Erro Auth user", $errorData);
              $responseData = [
                  'status' => false,
                  'mensage' => 'Try logging in again.',
                  'error' => $errorData,
              ];
            return response()->json(['success' => false, 'responseData' => $responseData], 500);
        }
    }

    private function guard()
    {
        return Auth::guard();
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();
            JWTAuth::setToken($token)->invalidate();
            Auth::logout();
            return response()->json(['message' => 'Logout completed successfully.']);
        } catch (\Exception $e) {
            $errorData = [
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'erro' => $e->getMessage(),
              ];
              Log::channel("auth")->error("Erro Auth logout", $errorData);
             
            return response()->json(['status' => 'error', 'message' => $errorData], 500);
        }
    }
}