<?php

namespace App\Http\Controllers;

use App\UserSocial;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redir()
    {
        return url();
    }
    public function login($provider)
    {
        return Socialite::with($provider)->stateless()->redirect();
    }

    public function callback($provider)
    {
        try {
            $oauth = (object) Socialite::with($provider)->stateless()->user();
            if($oauth)
            {
                $user_social = UserSocial::where('provider',$provider)
                    ->where('provider_id',$oauth->id)->first();

                if($user_social) {
                    // En caso se existir, actualiza el token de session
                    $user_social->token = $oauth->token;
                    $user_social->save();
                } else {
                    // Si no existe en la base de datos, crea un nuevo usuario social
                    $user_social = new UserSocial();
                    $user_social->provider = $provider;
                    $user_social->provider_id = $oauth->id;
                    $user_social->username = $oauth->name;
                    $user_social->email = $oauth->email;
                    $user_social->token = $oauth->token;
                    $user_social->save();
                }

                return compact('user_social','oauth');
            } else {
                return compact('oauth');
            }
        } catch(\Exception $ex)
        {
            return ['error' => $ex->getMessage()];
        }
    }
}
