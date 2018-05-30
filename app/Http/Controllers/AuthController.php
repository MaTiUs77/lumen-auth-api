<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'username'    => 'required|string|max:100',
            'password' => 'required',
        ]);

        try {

            $token = $this->jwt->attempt($request->only('username', 'password'));

            if ($token) {
                return response()->json(compact('token'));
            } else {
                return $this->invalid_credential();
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            $code = 500;
            $error = 'token_expired';
            $message  = 'Token expirado';
            $output = compact('code','error','message');
            return response()->json($output, $code);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $code = 500;
            $error = 'token_invalid';
            $message  = 'Token invalido';
            $output = compact('code','error','message');
            return response()->json($output, $code);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            $code = 500;
            $error = 'token_absent';
            $message  = $e->getMessage();
            $output = compact('code','error','message');
            return response()->json($output, $code);
        }
    }

    public function me() {
        $user = Auth::user();
        if($user)
        {
            return response()->json($user);
        } else
        {
            return $this->invalid_credential();
        }
    }

    public function logout() {
        Auth::logout();
        return response()->json(['message' => 'Sesion finalizada']);
    }

    public function refresh()
    {
        $token = Auth::refresh();
        return response()->json(compact('token'));
    }

    public function generatePassword() {
        return [
            'password' => Hash::make("1234")
        ];
    }

    private function invalid_credential()
    {
        $code = 401;
        $error = 'invalid_credentials';
        $message  = 'Credenciales invalidas';
        $output = compact('code','error','message');

        return response()->json($output, $code);

    }
}