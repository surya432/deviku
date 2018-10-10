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
            if (!is_null($resultCheck360) || !empty($resultCheck360)){
                $this->renameopenload360($resultCheck360,$resultCheck360."-360p.mp4");
                $content->mirror1 = $resultCheck360;
				$content->save();
            }
        }
        if(is_null($content->mirror1)){
            $video360p = $content->f720p;
            $openload360 = $this->iframesd($video360p);
            if(!is_null($openload360)){
                $content->mirror1 = $openload360;
				$content->save();
            }
        }
        if(preg_match("/upload_id=/",$content->mirror3)){
            $resultCheck720 = $this->check_openload720($content->mirror3);
            if (!is_null($resultCheck720) || !empty($resultCheck720)){
                $this->renameopenload720($resultCheck720,$resultCheck720."-720p.mp4");
                $content->mirror3 = $resultCheck720;
				$content->save();
            }
        }
        if(is_null($content->mirror3)){
            $video720p = $content->f720p;
            $openload720 = $this->iframesd($video720p);
            if(!is_null($openload720)){
                $content->mirror3 = $openload720;
	            $content->save();

            }
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
                    if(is_null($copyID) || isset($copyid['error']) ){ 
                        return abort(404);
                    };
                    return file_get_contents("http://db.nontonindrama.com/Player-Script/json.php?url=https://drive.google.com/open?id=".$copyID);
                }else{
                    return file_get_contents("http://db.nontonindrama.com/Player-Script/json.php?url=https://drive.google.com/open?id=".$mirror->idcopy);
                }
                break;
            case 'gd720':
                $mirror = Mirror::select('idcopy')->where('url',$content->f720p)->where('kualitas','HD')->first();
                //return json_encode($mirror);
                if(is_null($mirror)){
                    $copyID =$this->GDCopy($content->f720p, md5($url.$mytime),'HD');
                    if(is_null($copyID)){ 
                        return abort(404);
                    };
                    return file_get_contents("http://db.nontonindrama.com/Player-Script/json.php?url=https://drive.google.com/open?id=".$copyID);
                }else{
                    return file_get_contents("http://db.nontonindrama.com/Player-Script/json.php?url=https://drive.google.com/open?id=".$mirror->idcopy);
                    }
                break;
            case 'mirror1':
                $iframe = "http://oload.stream/embed/".$content->mirror1;
                return $iframe;
                break;
            case 'mirror2':
                $iframe = "http://www.rapidvideo.com/e/".$content->mirror2;
                return $iframe;
                break;
            case 'mirror3':
                $iframe = "http://oload.stream/embed/".$content->mirror3;
                return $iframe;
                break;
        }

    }
    
}
