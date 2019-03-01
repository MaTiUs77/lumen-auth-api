<?php

namespace App\Http\Controllers;

use App\UserSocial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialController extends Controller
{
    public function login($provider)
    {
        // Enviamos a oauth el nombre del app
        $app = Input::get('app');

        return Socialite::with($provider)
            ->stateless()
            ->with(['state'=>$app])
            ->redirect();
    }

    public function callback($provider)
    {
        // Nombre del App retornado desde oauth
        $app = Input::get('state');

        try {
            $oauth = (object) Socialite::with($provider)
                ->stateless()
                ->user();

            $jwt = null;
            if($oauth)
            {
                $user_social = UserSocial::where('provider',$provider)
                    ->where('provider_id',$oauth->id)
                    ->first();

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
                return $this->redireccionar($token,$app);
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

    public function logout() {
        Auth::logout();
        return response()->json(['message' => 'Sesion finalizada']);
    }

    public function refresh()
    {
        $token = Auth::refresh();
        return response()->json(compact('token'));
    }

    private function redireccionar($token,$app) {
        $url = url();
        $habilitado = false;

        if (strpos($url, 'siep-produccion') !== false) {
            $habilitado = true;
            switch($app)
            {
                case 'siep-pwa':
                    $url = 'https://inscribitepor.sieptdf.org';
                    break;
                case 'siep-admin':
                    $url = 'https://admin.sieptdf.org';
                    break;
            }
        }

        if (strpos($url, 'siep-desarrollo') !== false) {
            $habilitado = true;
            switch($app)
            {
                case 'siep-pwa':
                    $url = 'https://dev.inscribitepor.sieptdf.org';
                    break;
                case 'siep-admin':
                    $url = 'https://dev.admin.sieptdf.org';
                    break;
            }
        }
        
        if (strpos($url, 'siep-auth-api') !== false) {
            $habilitado = true;
            switch($app)
            {
                case 'siep-pwa':
                    $url = 'http://localhost:1337';
                    break;
                case 'siep-admin':
                    $url = 'http://localhost:1338';
                    break;
            }
        }

        if($habilitado) {
            header("Location: $url?token=$token");
        }

        $error = [
            'mensaje' => 'No fue posible redireccionar la peticion',
            'url' => $url,
            'app' => $app,
        ];

        return compact('error');
    }

    private function jwt_error($code,$error,$message){
        $output = compact('code','error','message');
        return $output;
    }
}
