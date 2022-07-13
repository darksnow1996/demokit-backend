<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ContentFile extends Model
{
    use HasFactory;
    protected $guarded = [
        '_id',
    ];

    // public function content(){
    //     return $this->belongsTo(ContentFile::class,'content_id',"_id");
    // }
}
