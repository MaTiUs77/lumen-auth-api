<?php

namespace App\Http\Controllers;

use App\UserSocial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialController extends Controller
{
    public function login($provider)
    {
        return Socialite::with($provider)->stateless()->redirect();
    }

    public function callback($provider)
    {
        try {
            $oauth = (object) Socialite::with($provider)->stateless()->user();
            $jwt = null;
            if($oauth)
            {
                $user_social = UserSocial::where('provider',$provider)
                    ->where('provider_id',$oauth->id)->first();

                if($user_social) {
                    // En caso de existir, loguea al usuario con JWT y retorna el token
                    $jwt = $this->jwtLogin($user_social);
                } else {
                    // Si no existe en la base de datos, crea un nuevo usuario social
                    $user_social = new UserSocial();
                    $user_social->provider = $provider;
                    $user_social->provider_id = $oauth->id;
                    $user_social->username = $oauth->name;
                    $user_social->email = $oauth->email;
                    $user_social->save();

                    // Loguea y retorna token
                    $jwt = $this->jwtLogin($user_social);
                }

                $token = $jwt['token'];
                return $this->redireccionar($token);
                //return compact('jwt','token');
            } else {
                return compact('oauth');
            }
        } catch(\Exception $ex)
        {
            return ['error' => $ex->getMessage()];
        }
    }

    private function jwtLogin(UserSocial $user)
    {
        Config::set('jwt.user', 'App\UserSocial');
        Config::set('auth.providers.users.model', \App\UserSocial::class);

        $token = null;

        try {
            $token = JWTAuth::fromUser($user);

            if ($token) {
                $response = 'success';
                $output = compact('response','token');
                return $output;
            } else {
                return $this->jwt_error(401,'invalid_credentials','Credenciales invalidas');
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->jwt_error(500,'token_expired','Token expirado');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->jwt_error(500,'token_invalid','Token invalido');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->jwt_error(500,'token_absent',$e->getMessage());
        }
    }

    public function me() {
        $user = Auth::guard('social')->user();
        if($user)
        {
            return response()->json($user);
        } else
        {
            return $this->jwt_error(401,'token_invalid','Token invalido');
        }
    }

    private function redireccionar($token) {
        $url = url();

        if (strpos($url, 'siep-produccion') !== false) {
            $url = 'https://inscribitepor.sieptdf.org';
        }

        if (strpos($url, 'siep-desarrollo') !== false) {
            $url = 'https://dev.inscribitepor.sieptdf.org';
        }
        
        if (strpos($url, 'siep-auth-api') !== false) {
            $url = 'http://localhost:1337';
        }

        header("Location: $url?token=$token");
    }

    private function jwt_error($code,$error,$message){
        $output = compact('code','error','message');
        return $output;
    }
}
