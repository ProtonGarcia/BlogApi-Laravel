<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;

class TestController extends Controller
{
    public function index()
    {
        $titulo = 'TestController';
        $animales = ['perro', 'gato', 'lagarto'];

        return view('pruebas.index', compact('titulo', 'animales'));
        /*
        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
        */
    }

    public function testOrm()
    {
        $posts = Post::all();

        /*foreach ($posts as $post) {
            echo "<h2>" . $post->titulo . "</h2>";
            echo "<span>" . $post->user->name ." - ".$post->category->name ."</span>";
            echo "<p>" . $post->content. "</p><br/>";
        }*/

        $categories = Category::all();

        foreach ($categories as $category) {
            echo "<h2>" . $category->name . "</h2>";

            foreach ($category->posts as $post) {
                echo "<h3>" . $post->titulo . "</h3>";
                echo "<span>" . $post->user->name ." - ".$post->category->name ."</span>";
                echo "<p>" . $post->content. "</p><br/>";
            }
        }

        die();
    }
}
