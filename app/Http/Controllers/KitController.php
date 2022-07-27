<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentFileRequest;
use App\Http\Requests\KitRequest;
use App\Models\Content;
use App\Models\ContentFile;
use App\Models\File;
use App\Models\Kit;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

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
        $kits =$user->kits()->orderBy('created_at','desc')->get();
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
        $content->description  = $request->description;
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

        $contents = $kit->contents()->orderBy('created_at','desc')->get();
        return response()->json(
            [
                "data" => $contents
            ], Response::HTTP_OK);

    }

    public function uploadContent(ContentFileRequest $request,$id,$cid){
        $kit = auth()->user()->kits()->find($id);
        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], Response::HTTP_NOT_FOUND);
        }
        $content = $kit->contents()->find($cid);
        if(!$content){
            return response()->json(
                [
                    "message" => "Content not found"
                ], Response::HTTP_NOT_FOUND);
        }
        $type = $content->type;
        $filename = "DK_".date('mdYHis') . uniqid();
        $folder = "kit_".$kit->_id."/content/";
        $fileextension = $request->file->extension();

       // dd(env('AWS_URL'));
        $path = Storage::disk('s3')->putFileAs($folder, $request->file,$filename.".".$fileextension);

        $path = Storage::disk('s3')->url($path);
       if($path){
        $file = new ContentFile;
        $file->name = $filename;
        $file->path = $path;
        $file->ext = $fileextension;
      //  dd($content);
        $content->contentfile()->save($file);

        return response()->json(
            [
                "message" => "File Upload successfully"
            ], 200);

       }

       return response()->json(
        [
            "message" => "Unable to complete upload"
        ], Response::HTTP_SERVICE_UNAVAILABLE);

    }


    public function publishKit($id){
        $kit = Kit::find($id);
        if(!$kit){
            return response()->json(
                [
                    "message" => "Kit not found"
                ], Response::HTTP_NOT_FOUND);
        }
        if($kit->published_at){
            return response()->json(
                [
                    "message" => "Kit already published"
                ], Response::HTTP_OK);
        }
        $kit->published_at= Carbon::now()->timestamp;
        $kit->save();

        return response()->json(
            [
                "result" => "Kit Successfully published"
            ], 201);
    }
}
