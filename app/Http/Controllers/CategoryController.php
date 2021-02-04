<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('Api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (is_object($category)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'categories' => $category
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'no se encontro la categoria'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $req)
    {
        /**
         * Al ser una ruta de tipo resource los 
         * middlewares deben ser cargados la clase contrstructor
         * para aplicarse a un metodo en especifico
         */

        //Recoger los datos por post

        $json = $req->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Validar los datos

            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            //Guardar la categoria

            if ($validate->fails()) {
                $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se pudo ingresar la categoria.'
                );
            } else {
                $category = new Category();

                $category->name = $params_array['name'];

                $category->save();

                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                );
            }
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'La categoria debe tener un nombre.'
            );
        }

        //devolver los resultados
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $req)
    {
        //reocger los datos
        $json = $req->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //validar los datos
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            //quitar lo que no se desea actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

            // actualizar el registros
            $category = Category::where('id', $id)->update($params_array);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'La categoria debe tener un nombre.'
            );
        }


        // devolver los datos
        return response()->json($data, $data['code']);
    }
}
