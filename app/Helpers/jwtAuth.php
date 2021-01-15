<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = 'esta-es-la-clave-secreta0025';
    }

    public function signUp($email, $password, $getToken = null)
    {
        /**
         * hacer un provider JwtAuthServiceProvider
         */



        /**
         * Buscar si existe el usuario con las credenciales
         */

        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();



        /**
         * Comprobar si los datos del usuario son correctos
         */

        $signUp = false;

        if (is_object($user)) {
            $signUp = true;
        }
        /**
         * Generar el token con los datos del usuario identificado
         */

        if ($signUp) {
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );

            //guardando el token
            $jwt = JWT::encode($token, $this->key, 'HS256');

            $decode = JWT::decode($jwt, $this->key, ['HS256']);

            /**
             * Devolver los datos decodificados o el token en funcion de un parametro
             */
            if (is_null($getToken)) {
                $data = $jwt;
            } else {
                $data = $decode;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto'
            );
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;

        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $exp) {
            $auth = false;
        } catch (\DomainException $exp) {
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }
}
