<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use App\Drama;
use App\Content;
use Cache;
use App\Type;
use Yajra\DataTables\Facades\DataTables;
class GDController extends Controller
{
    //
    use HelperController;
    public function singkron($id){
        if(Cache::get('Drama')){
            $value =Cache::get('Drama')->where('id',$id)->first();
        }else{
            $value = Drama::with('country')->with('type')->with('eps')->orderBy('id','desc')->get();
            Cache::forever('Drama',$value);
            $value = Cache::get('Drama')->where('id',$id)->first();
        }
        if($value){
            $folderId= $value->folderid;
        }else{
            $folderId= $id;
        }
        $resultCurl = $this->singkronfile($folderId);
        $fdrive =array(); 

        foreach($resultCurl['files'] as $Nofiles){
            if(preg_match("/-720p.mp4/",$Nofiles['name'])){
                $url = str_replace('-720p.mp4','', $Nofiles['name']);
                $content = Content::where('url', $url)->first();
                if($content){
                    if($content->f720p !="https://drive.google.com/open?id=".$Nofiles['id'] ){
                        $content->f720p = "https://drive.google.com/open?id=".$Nofiles['id'] ;
                        $content->save();
                        $data = Content::orderBy('id','desc')->where('drama_id',$id)->get();
                        Cache::forever('Content'.$id,$data);
                        array_push($fdrive,$url);
                    }
                }
            }elseif(preg_match("/-360p.mp4/",$Nofiles['name'])){
                $url = str_replace('-360p.mp4','', $Nofiles['name']);
                $content = Content::where('url', $url)->first();
                if($content){
                    if($content->f360p !="https://drive.google.com/open?id=".$Nofiles['id'] ){
                        $content->f360p = "https://drive.google.com/open?id=".$Nofiles['id'] ;
                        $content->save();
                        $data = Content::orderBy('id','desc')->where('drama_id',$id)->get();
                        Cache::forever('Content'.$id,$data);
                        array_push($fdrive,$url);
                    }
                }
            }                 
        }
        return view('dashboard.singkronContent')->with('url', $fdrive); 
    }
}
