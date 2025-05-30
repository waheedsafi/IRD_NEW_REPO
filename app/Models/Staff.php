<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Staff extends Model
{
    protected $guarded = [];
    public function staffTran()
    {
        return $this->hasMany(StaffTran::class);
    }
}
