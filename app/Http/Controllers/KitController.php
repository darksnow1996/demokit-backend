<?php

namespace App\Http\Controllers;

use App\Http\Requests\KitRequest;
use App\Models\Content;
use App\Models\Kit;
use App\Models\Service;
use App\Models\User;
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
       $kit->is_public = false;
       if($author->kits()->save($kit)){
        return response()->json(
            [
                "data"=> [
                    "_id" => $kit->_id,
                    "title" => $kit->title,
                ],
                "message" => "Demo Kit created successfully"
            ], 200);
        }
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

    public function getMyKits(){
        try{
        $user = auth()->user();
      //  var_dump($user)
        $kits =$user->kits()->get();
       // $kit = Kit::find('62c8afa5d570b0aa660f81a0');
        //var_dump($kit);

        return response()->json(
            [
                "data" => $kits
            ], Response::HTTP_OK);
        }
        catch(Exception $e){
            return response()->json(
                [
                    "message" => $e->getMessage()
                ], $e->getCode());

        }
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
      // var_dump("here");
        $kit = Kit::find($id);
        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], Response::HTTP_NOT_FOUND);
        }
        //delete all services associated with this kit
        //change the level
        //get services and put them in the kit
        $kit->services()->delete();
        $kit->level = $request->level;


        if($request->services){

            foreach($request->services as $service){
                $service = Service::find($service);
                 //var_dump($service->name);
                $kit->push('services',[
                    '_id' => $service->_id,
                    "name" => $service->name,
                ],true);
            }
        }


        if($kit->isDirty()){
            $kit->save();

            return response()->json(
                [
                    "message" => "Demo Kit updated successfully"
                ], 200);

        }

        return response()->json(
            [
                "message" => "No changes made"
            ], 200);

     //  var_dump($service);

    //    $kitUpdate = $kit->services()->create(
    //     ['_id'=> '62ca5c32d570b0aa660f81a7',
    //     'name' => 'Amazon EC2']);

    // $kit->push('services', [
    //     'name' => 'MyTest1',
    //     '_id' => '62ca5c32d570b0aa660f81a7'

    // ],true);
    }

    public function addContent(Request $request,$id){
        $kit = Kit::find($id);
        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], Response::HTTP_NOT_FOUND);
        }
        $content = new Content;

        $content->title  = $request->title;
        $content->duration  = $request->duration;
        $content->level = $request->level;
        $content->type = $request->type;
        $content->link = $request->link;

        $kit->contents()->save($content);

        return response()->json(
            [
                "result" => "Content created successfully"
            ], 201);
    }


    public function getContents($id){
        $kit = Kit::find($id);
        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], Response::HTTP_NOT_FOUND);
        }

        $contents = $kit->contents()->get();
        return response()->json(
            [
                "data" => $contents
            ], Response::HTTP_OK);

    }
}
