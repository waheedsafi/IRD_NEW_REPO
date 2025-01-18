<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NgoTran extends Model
{
    protected $guarded = [];

    public function ngo()
    {
        return $this->belongsTo(Ngo::class, 'ngo_id');
    }
}
