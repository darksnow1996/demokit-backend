<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    public function kit(){
        return $this->belongsTo(Kit::class,'kit_id');
    }

    public function contentfile(){
        return $this->embedsOne(ContentFile::class);
    }



}
