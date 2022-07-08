<?php

namespace App\Http\Controllers;

use App\Http\Requests\KitRequest;
use App\Models\Kit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KitController extends Controller
{
    public function create(KitRequest $request){
        try{
        $author = auth()->user();
        $kit = new Kit;


        $kit->title = $request->title;
        $kit->author = [
            "_id" => $author->_id,
            "name" => $author->name,
            "email" => $author->email,
        ];
        $kit->is_public = false;
      //  $post->slug = $request->slug;

        $kit->save();

        return response()->json(
            [
                "data"=> [
                    "_id" => $kit->_id,
                    "title" => $kit->title,
                ],
                "message" => "Demo Kit created successfully"
            ], 200);
        }
        catch(Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ], $e->getCode());

        }
    }


    public function basicInfo(Request $request,$id){
        $kit = Kit::find($id);
        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], 404);
        }

        $kit->title = $request->title;
        $kit->desc =$request->desc;
      //  $post->slug = $request->slug;

      if($kit->isDirty()){
        $kit->save();

        return response()->json(
            [
                "message" => "Demo Kit updated successfully"
            ], 201);

      }

      return response()->json(
        [
            "message" => "No changes made"
        ], 201);


    }

    public function allKits(){
        $kits = Kit::all();

        return response()->json(
            [
                "data" => $kits
            ], Response::HTTP_OK);
    }

    public function getKit($id){
        $kit = Kit::find($id);

        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], Response::HTTP_NOT_FOUND);
        }
        return response()->json(
            [
                "data" => $kit
            ], Response::HTTP_OK);
    }


    public function addMetadata(Request $request,$id){
        $kit = Kit::find($id);

        $kit->metadata = $request->metadata;

        $kit->save();

        return response()->json(
            [
                "result" => "Demo Kit updated successfully"
            ], 201);
    }

    public function addContent(Request $request,$id){
        $kit = Kit::find($id);

        $kit->content = $request->content;

        $kit->save();

        return response()->json(
            [
                "result" => "Demo Kit updated successfully"
            ], 201);
    }
}
