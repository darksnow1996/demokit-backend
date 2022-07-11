<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function getServices(){
        $services = Service::all();
        return response()->json([
            "data" => $services
        ], 200);
        

    }
}
