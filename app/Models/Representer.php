<?php

namespace App\Models;

use App\Traits\template\Auditable;
use Illuminate\Database\Eloquent\Model;

class Representer extends Model
{
    //
    use Auditable;
    protected $guarded = [];
}
