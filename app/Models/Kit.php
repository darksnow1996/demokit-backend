<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class Kit extends Model
{
    use HasFactory;

    public function author(){
        return $this->embedsOne(User::class);
    }

    public function contents(){
        return $this->hasMany(Content::class);
    }
}
