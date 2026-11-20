<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'body', 'show_in_footer', 'sort_order'];

    protected $casts = ['show_in_footer' => 'boolean'];
}
