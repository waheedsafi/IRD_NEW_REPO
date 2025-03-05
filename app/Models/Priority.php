<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $guarded = [];

    public function priorityTran()
    {
        return $this->hasMany(PriorityTrans::class, 'priority_id');
    }
}
