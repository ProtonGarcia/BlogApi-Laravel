<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('Api.auth', ['except' => ['index', 'show', 'getImage', 'getPostsByUser', 'getPostByCategory']]);
    }

    public function index()
    {
        /*Al usar el load con el modelo de category
         se creara un objeto en la respuesta con la 
         informacion de la categoria */

        $posts = Post::all()->load('category');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function show($id)
    {
        $post = Post::find($id)->load('category');

        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No se encontro la publicacion'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $req)
    {

        //el usuario ya esta autenticado por el middleware
        //recoger datos post
        $json = $req->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            /**
             * conseguir el usuario identificado
             * para esto se importo el helper de JWTauth 
             */

            //conseguir el usuario
            $user = $this->getIdentity($req);

            //validar los datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => ' debes llenar todos los campos'
                ];
            } else {
                //guardar el post
                $post = new Post();

                $post->user_id = $user->sub;
                $post->category_id = $params_array['category_id'];
                $post->title = $params_array['title'];
                $post->content = $params_array['content'];
                $post->image = $params_array['image'];

                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => ' debes llenar todos los campos'
            ];
        }


        //devolver la respuesta

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $req)
    {

        $data = [
            'code' => 400,
            'status' => 'error',
            'message' => 'debes llenar los campos obligatorios'
        ];

        //recoger los datos por post
        $json = $req->input('json', null);

        $params_array = json_decode($json, true);

        //conseguir el usuario
        $user = $this->getIdentity($req);

        if (!empty($params_array)) {
            //validar los datos

            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
            }
            //eliminar lo que no se desea actualizar 
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['user']);


            $post = Post::where('id', $id)->where('user_id', $user->sub)->first();

            if (!empty($post) && is_object($post)) {
                //actualizar, con updateOrCreate conseguimos los datos previos a actualizar
                $where = ['id' => $id, 'user_id' => $user->sub];

                $changes = Post::updateOrCreate($where, $params_array);

                //devolver los datos
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $changes
                ];
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No puedes modificar algo que no te pertenece'
                ];
            }
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $req)
    {
        //conseguir el usuario
        $user = $this->getIdentity($req);

        //Conseguir el post
        $post = Post::where('id', $id)
            ->where('user_id', $user->sub)
            ->first();

        if (!empty($post)) {
            //borrarlo
            $post->delete();

            //devolver informacion
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No se encontro el post a eliminar'
            ];
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Esta funcion usara el JWT para conseguir la informacion 
     * del usuario loggeado
     */
    private function getIdentity($req)
    {
        //Conseguir el usuario
        $jwtAuth = new JwtAuth();
        $token = $req->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $req)
    {
        //recoger la peticion
        $image = $req->file('file0');

        //validar la imagen
        $validate = \Validator::make($req->all(), [
            'file0' => 'required|image|mimes: jpg,jpeg,png,gif'
        ]);

        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        } else {
            //guardar la imagen
            $image_name = time() . $image->getClientOriginalName();

            \Storage::disk('images')->put($image_name, \File::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }

        //devolver los datos
        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {
        //comprobar que exista la imagen
        $image = \Storage::disk('images')->exists($filename);

        if ($image) {
            //conseguir la imagen

            $file = \Storage::disk('images')->get($filename);

            //devolver informacion

            return new Response($file, 200);
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getPostByCategory($id)
    {
        $posts = Post::where('category_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function getPostsByUser($id)
    {
        $posts = Post::where('user_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
