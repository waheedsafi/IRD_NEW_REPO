<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsType extends Model
{
    protected $guarded = [];


    public function newsTypeTran()
    {
        return $this->hasMany(NewsTypeTrans::class, 'news_type_id');
    }
}
