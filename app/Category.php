<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name'
    ];

    /**
     * Relacion de uno a muchos
     */
    public function posts()
    {
        return $this->hasMany('app\Post');
    }
}
