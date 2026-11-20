<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'sort_order', 'show_in_menu', 'is_active'];

    protected $casts = ['show_in_menu' => 'boolean', 'is_active' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function getFullNameAttribute(): string
    {
        $names = [$this->name];
        $p = $this->parent;
        while ($p) {
            array_unshift($names, $p->name);
            $p = $p->parent;
        }

        return implode(' › ', $names);
    }

    public function descendantIds(): array
    {
        $ids = [$this->id];
        $all = Region::select('id', 'parent_id')->get()->groupBy('parent_id');
        $stack = [$this->id];
        while ($stack) {
            $cur = array_pop($stack);
            foreach ($all->get($cur, collect()) as $child) {
                $ids[] = $child->id;
                $stack[] = $child->id;
            }
        }

        return $ids;
    }

    public static function flatOptions()
    {
        $all = Region::with('parent.parent')->orderBy('sort_order')->orderBy('name')->get();

        return $all->sortBy('full_name')->values();
    }
}
