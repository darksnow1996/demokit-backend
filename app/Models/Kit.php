<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class Kit extends Model
{
    use HasFactory;

    protected $guarded = [
        '_id',
    ];
    protected $dates = ['published_at'];
    // protected $casts = [
    //     'user_id' => ObjectIDCast::class,

    // ];


    public function author(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contents(){
        return $this->hasMany(Content::class, 'kit_id','_id');
    }

    public function services(){
        return $this->embedsMany(Service::class);
    }
}
