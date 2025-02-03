<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    protected $guarded = [];

    protected $casts = [
        'acceptable_mimes' => 'array', // Automatically cast the 'tags' column to an array
    ];
}
