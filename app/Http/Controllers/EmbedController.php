<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Content;
use App\Mirror;
use DB;
use Cache;
use Jenssegers\Agent\Agent;
use GeoIP;
class EmbedController extends Controller
{
    //
    use HelperController;
    public function index(Request $request,$url){
        $contentCheck = Content::where('url',$url)->first();
        if(is_null($contentCheck) || is_null($contentCheck->f720p)){
            return abort(404);
        }
        $agent = new Agent();
        $location = GeoIP::getLocation();
        $country=$location->iso_code;
        if($country == "KR" || $country == "US" && !$agent->isMobile() || $country == "US" && !$agent->isTablet() ){
        //if($country == "KR" ){
            return abort(404);
        }

        $value= $this->MirrorCheck($url);
        return view("embed.index")->with("url", $value)->with('GeoIP',$country);
    }
    function MirrorCheck($url){
        $content = Content::where('url',$url)->first();
        $save = false;
        if(preg_match("/upload_id=/",$content->mirror1)){
            $resultCheck360 = $this->check_openload360($content->mirror1);
            if (!is_null($resultCheck360) || !empty($resultCheck360) || $resultCheck360 != ""){
                $this->renameopenload360($resultCheck360,$resultCheck360."-360p.mp4");
                $save = true;
                $content->mirror1 = $resultCheck360;
            }
        }
        if(is_null($content->mirror1) || $content->mirror1 == "0" || $content->mirror1 ==""){
            $video360p = $content->f360p;
            $openload360 = $this->iframesd($video360p);
            if(!is_null($openload360)){
                $save = true;
                $content->mirror1 = $openload360;
            }
        }else{
            $openload360 = $this->checkfile_openload360($content->mirror1);
            if(is_null($openload360)){
                $save = true;
                $content->mirror1 = null;
            }
        }
        if(preg_match("/upload_id=/",$content->mirror3)){
            $resultCheck720 = $this->check_openload720($content->mirror3);
            if (!is_null($resultCheck720) || !empty($resultCheck720) || $resultCheck720 != ""){
                $this->renameopenload720($resultCheck720,$resultCheck720."-720p.mp4");
                $save = true;
                $content->mirror3 = $resultCheck720;
            }
        }
        if(is_null($content->mirror3) || $content->mirror3 =="" || $content->mirror3 =="0"){
            $video720p = $content->f360p;
            $openload720 = $this->iframehd($video720p);
            if(!is_null($openload720)){
                $save = true;
                $content->mirror3 = $openload720;
            }
        }else{
            $openload720 = $this->checkfile_openload720($content->mirror3);
            if(is_null($openload720)){
                $save = true;
                $content->mirror3 = null;
            }
        }
        if($save){
            $content->save();
        }
        return $content;
    }
    public function getDetail(Request $request,$url){
        $content = Content::where('url',$url)->first();
        $mytime = \Carbon\Carbon::now();
        $this->AutoDeleteGd();
        switch($request->input('player')){
            case 'gd360':
                $mirror = Mirror::select('idcopy')->where('url',$content->f360p)->where('kualitas','SD')->first();
                //return json_encode($mirror);
                if(is_null($mirror)){
                    $copyID =$this->GDCopy($content->f360p, md5($url.$mytime),'SD');
                    if(is_null($copyID) || isset($copyID['error']) ){ 
                        return abort(404);
                    };
                    //return file_get_contents("http://db.nontonindrama.com/Player-Script/json.php?url=https://drive.google.com/open?id=".$copyID);
                    return file_get_contents("https://dldramaid.xyz/player/json.php?url=https://drive.google.com/open?id=".$copyID);
                }else{
                    //return file_get_contents("http://db.nontonindrama.com/Player-Script/json.php?url=https://drive.google.com/open?id=".$mirror->idcopy);
                    return file_get_contents("https://dldramaid.xyz/player/json.php?url=https://drive.google.com/open?id=".$mirror->idcopy);
                }
                break;
            case 'gd720':
                $mirror = Mirror::select('idcopy')->where('url',$content->f720p)->where('kualitas','HD')->first();
                //return json_encode($mirror);
                if(is_null($mirror)){
                    $copyID =$this->GDCopy($content->f720p, md5($url.$mytime),'HD');
                    if( is_null($copyID) || isset($copyID['error']) ){ 
                        return abort(404);
                    };
                    return file_get_contents("https://dldramaid.xyz/player/json.php?url=https://drive.google.com/open?id=".$copyID);
                }else{
                    return file_get_contents("https://dldramaid.xyz/player/json.php?url=https://drive.google.com/open?id=".$mirror->idcopy);
                    }
                break;
            case 'mirror1':
                $iframe = "https://oload.stream/embed/".$content->mirror1;
                return $iframe;
                break;
            case 'mirror2':
                $iframe = "https://www.rapidvideo.com/e/".$content->mirror2;
                return $iframe;
                break;
            case 'mirror3':
                $iframe = "https://oload.stream/embed/".$content->mirror3;
                return $iframe;
                break;
			case "download_links":
                $returncontent = "";
                $returncontent .= '<div id="notif" class="text-center"><p style="color: blue;">';
                if(!is_null($content->mirror1)){
                    if(!preg_match("/upload_id=/",$content->mirror1)){
                    $returncontent .= "<a href='https://oload.stream/f/".$content->mirror1."' class='btn btn-sm btn-primary' target='_blank'>Openload 360p</a>";
                    }
                }
                if(!is_null($content->mirror3)){
                    if(!preg_match("/upload_id=/",$content->mirror3)){
                    $returncontent .= "<a href='https://oload.stream/f/".$content->mirror3."' class='btn btn-sm btn-primary' target='_blank'>Openload 720p</a>";
                    }
                }
                /* if(!is_null($content->mirror2)){					
                    if(!preg_match("/upload_id=/",$content->mirror2)){
                    $returncontent .= "<a href='http://www.rapidvideo.com/d/".$content->mirror2."' class='btn btn-sm btn-primary' target='_blank'>RapidVideo 720p</a>";
                    }
                } */
                $returncontent .= '</p></div>';
                return $returncontent;
                break;
        }

    }
    
}
