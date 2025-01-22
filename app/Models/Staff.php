<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Staff extends Model
{
    //


    protected $guarded = [];

    public function staffTranDefault(){
        return $this->hasMany(StaffTran::class)->where('language_name',App::getLocale());
    }

     public function staffTran(){
        return $this->hasMany(StaffTran::class);
    }

    public function staffType(){
        return $this->belongsTo(StaffType::class);
    }

}
