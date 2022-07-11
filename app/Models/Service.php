<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $guarded = [
        '_id',
    ];
    protected $hidden = ['created_at', 'updated_at'];
}
