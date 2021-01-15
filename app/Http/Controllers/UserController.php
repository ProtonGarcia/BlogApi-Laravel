<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function pruebas(Request $req)
    {
        return "Accion de pruebas de userController";
    }

    public function register(Request $req)
    {

        /**
         * Recoger los datos del usuario
         * 
         * en este casp recibiremos un json desde el frontend
         */

        $json = $req->input('json', null);

        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true); //array


        if (!empty($params_array) && !empty($params)) {
            /**
             * Limpiar los datos o hacer trim
             */

            $params_array = array_map('trim', $params_array);

            /**
             * Validar los datos
             * 
             * usar la libreria validator
             */

            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users', //Al ser unique hace las validaciones para que los usuarios no se dupliquen
                'password' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' =>   400,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {

                /**
                 * Cifrar contraseña
                 *  $pwd = password_hash($params->password, PASSWORD_BCRYPT, [
                 *  'cost' => 4
                 * ]);
                 */

                $pwd = hash('sha256', $params->password);


                /**
                 * Crear el usuario
                 * 
                 * se incluye el modelo de usuario para 
                 * hacer la instancia y rellenar las propiedades
                 */

                $user = new User();

                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->description = $params_array['description'];
                $user->role = 'ROLE_USER';

                /**
                 * Guardar el usuario
                 */

                $user->save();


                $data = array(
                    'status' => 'success',
                    'code' =>   200,
                    'message' => 'El usuario se ha creado con exito',
                    'user' => $user
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' =>   400,
                'message' => 'datos enviados no son correctos'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function login(Request $req)
    {
        $jwtAuth = new \JwtAuth();

        /**
         * Recibir datos por post
         */

        $json = $req->input('json', null);

        $params = json_decode($json);

        $params_array = json_decode($json, true);

        /**
         * validar los datos
         * 
         * similar a lo que se hizo en la parte de arriba
         */

        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            $userSingUp = array(
                'status' => 'error',
                'code' =>   400,
                'message' => 'El usuario no se ha loggeado',
                'errors' => $validate->errors()
            );
        } else {
            /**
             * Cifrar la contraseña
             */

            $pwd = hash('sha256', $params->password);

            /**
             * Devolver el token y datos
             */

            $userSingUp = $jwtAuth->signUp($params->email, $pwd);

            if (!empty($params->gettoken)) {
                $userSingUp = $jwtAuth->signUp($params->email, $pwd, true);
            }
        }




        /**
         * Pruebas
         * 
         *$email = 'chepe2@gmail.com';
         *$pass = 'chepe';
         *$passDecode = hash('sha256', $pass);
         */



        return response()->json($userSingUp, 200);
    }

    public function update(Request $req)
    {
        /**
         * Actualizar usuario
         *
         * Comprobar si el usuario esta autenticado
         
         *Esto se movio al middleware de ApiAuthMiddleware

         * $token = $req->header('Authorization');

         * $jwtAuth = new \JwtAuth();

         * $checkToken = $jwtAuth->checkToken($token);
         */


        /**
         * Recoger datos por post
         */
        $json = $req->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {

            /**
             * Conseguir el usuario identificado
             */
            $user = $jwtAuth->checkToken($token, true);

            /**
             * Validar los datos
             */
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users,' . $user->sub
            ]);

            /**
             * Quitar campos que no se actualizaran
             */

            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            /**
             * Actualizar usuario en la bd
             * 
             */

            $user_update = User::where('id', $user->sub)->update($params_array);

            /**
             * Devolver array con el resultado
             */

            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => $user,
                'changes' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no esta identificado'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $req)
    {
        /**
         * Recoger los datos de la peticion
         */
        $image = $req->file('file0');

        /**
         * Validacion de imagen
         */

        $validate = \Validator::make($req->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        /**
         * Guardar imagenes
         */
        if (!$image || $validate->fails()) {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'image' => 'no se puede subir la imagen, seleccionar bien la imagen'

            );
        } else {
            $image_name = time() . $image->getClientOriginalName();
            //crear los discos virtuales en la carpeta storage/app
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name,

            );
        }
        /**
         * Devolver el resultado
         */


        return response()->json($data, $data['code']);
        //return response($data, $data['code'])->header('Content-type', 'text/plain');
    }

    public function getImage($filename)
    {
        $image = \Storage::disk('users')->exists($filename);

        if ($image) {
            $file = \Storage::disk('users')->get($filename);

            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'image' => 'No se encontro la imagen',

            );
        }

        return response()->json($data, $data['code']);
    }

    public function details($id)
    {
        $user = User::find($id);

        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        }else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'No se encontro al usuario.'
            );
        }

        return response()->json($data, $data['code']);
    }
}
