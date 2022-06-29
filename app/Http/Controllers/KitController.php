<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use Illuminate\Http\Request;

class KitController extends Controller
{
    public function create(Request $request){
        $kit = new Kit;

        $kit->title = $request->title;
        $kit->is_public = false;
      //  $post->slug = $request->slug;

        $kit->save();

        return response()->json(
            [
                "result" => "Demo Kit created successfully"
            ], 200);
    }


    public function basicInfo(Request $request){
        $kit = Kit::find($request->id);

        $kit->title = $request->title;
        $kit->desc =$request->desc;
      //  $post->slug = $request->slug;

      if($kit->isDirty()){
        $kit->save();

        return response()->json(
            [
                "result" => "Demo Kit updated successfully"
            ], 201);

      }

      return response()->json(
        [
            "result" => "No changes made"
        ], 201);


    }
}
