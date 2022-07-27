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
        $kit = Kit::where('_id', $id)->with("contents")->with("author")->first();
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

    public function searchCatalog(Request $request,Kit $kit){
        $kit = $kit->newQuery();
        if($request->has('q') && $request->q){
            $q = $request->q;
            $kit->where('title', 'like', '%'.$q.'%');
        }
        if($request->has('level') && $request->level){
            $level = $request->level;
             $kit->where('level', $level);
        }
        if($request->has('services')){
            $services = $request->services;
            // $kit->whereHas('services', function ($query) use ($request) {
            //     var_dump("here");
            //     $query->whereIn('services', $request->services);
            // });
             $kit->where('services._id','all',  $services);
        }

        $kit = $kit->whereNotNull('published_at')->with('author')->orderBy('published_at','desc')->get();

        return response()->json(
            [
                "data"=> $kit
            ], 200);

    }
}

