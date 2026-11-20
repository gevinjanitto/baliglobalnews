<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'show_in_menu', 'show_on_home', 'is_active'];

    protected $casts = ['show_in_menu' => 'boolean', 'show_on_home' => 'boolean', 'is_active' => 'boolean'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
