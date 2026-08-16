<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'completed', 'category_id', 'priority', 'attachment'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
