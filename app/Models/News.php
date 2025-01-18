<?php

namespace App\Models;

use App\Models\NewsDocument;
use App\Models\NewsType;
use App\Models\Priority;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    //


    protected $guarded = [];




    
   
    public function newsType(){

        return $this->belongsTo(NewsType::class,'news_type_id');
    }

      public function priority(){

        return $this->belongsTo(Priority::class,'priority_id');
    }

    public function newsDocument(){
        
        return $this->hasMany(NewsDocument::class);
    }

    public function newsTran(){

        return $this->hasMany(NewsTran::class);
   
    }




}
