<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    //

    public function getCatalogs(){
        $catalogs = Kit::all();
        return response()->json(
            [
                "data"=> $catalogs
            ], 200);
    }


    public function getKit($id){
        $kit = Kit::where('_id', $id)->with("contents")->first();
        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], 404);
        }

        return response()->json(
            [
                "data"=> $kit
            ], 200);
    }
}
