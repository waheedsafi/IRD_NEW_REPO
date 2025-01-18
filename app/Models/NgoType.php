<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NgoType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ngoTypeTrans()
    {
        return $this->hasMany(NgoTypeTrans::class);
    }
}
