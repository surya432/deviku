<?php 
namespace App\Http\Controllers;
use Cache;
use App\Gmail;
use DB;

use App\Mirror;
trait HelperController {

    function seoUrl($string) {

        $string = trim($string); // Trim String
    
        $string = strtolower($string); //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
    
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);  //Strip any unwanted characters
    
        $string = preg_replace("/[\s-]+/", " ", $string); // Clean multiple dashes or whitespaces
    
        $string = preg_replace("/[\s_]/", "-", $string); //Convert whitespaces and underscore to dash
    
        return $string;
    
    }
    function viewsource($url){
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $head[] = "Connection: keep-alive";
        $head[] = "Keep-Alive: 300";
        $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $head[] = "Accept-Language: en-us,en;q=0.5";
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_REFERER, 'http://dldramaid.xyz/');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }
    function singkronfile($id_folder,$tokenPage= null){
        $tokenPages = "";
        if($tokenPage){
            $tokenPages = "&pageToken=".base64_decode($nextpage);
        }
        $curl= $this->viewsource("https://www.googleapis.com/drive/v3/files?q='".$id_folder."'+in+parents&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&&pageSize=250&orderby=modifiedByMeTime".$tokenPages);
        return json_decode($curl,true);
    }
    function singkronToWeb($id_folder,$tokenPage= null){
        $tokenPages = "";
        if($tokenPage){
            $tokenPages = "&pageToken=".base64_decode($nextpage);
        }
        $curl= $this->viewsource("https://www.googleapis.com/drive/v3/files?q='".$id_folder."'+in+parents&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&&pageSize=250&orderby=modifiedByMeTime".$tokenPages);
        return json_decode($curl,true);
    }
    function getEmbed($data){
        $embed= '';
        foreach($data as $eps){
            $embed .=
			'<h3>'.$eps->title.'</h3>
            <p><iframe src="'.route("viewEps",$eps->url).'" width="100%" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>';
        }
        return $embed;
    }
    function GetTags($data){
        $embed= '';
        $eps = $data->eps;
        $title = $data->title;
        $category = $data->country->name;
        $jenis = $data->type->name;
        $tag="Nonton ".$title." Subtile Indonesia, Nonton ".$title." Sub Indonesia, Nonton ".$jenis." ".$category." ".$title." Sub Indo, Nonton ".$jenis." ".$category." ".$title." Subtitle Indo online, Nonton ".$title." Sub Indonesia Online, Nonton ".$jenis." ".$category." Sub Indo, Nonton ".$jenis." ".$category." Subtitle Indo, Nonton ".$jenis." ".$title." Subtitle Indonesia, Download ".$jenis." ".$category." ".$title." Sub Indo, Download ".$title." Sub Indonesia, Download ".$title." ".$category." Subtitle Indonesia, Download ".$jenis." ".$title." Subtitle Indonesia, Download ".$category." ".$title." Subtitle Indonesia, Download ".$jenis." ".$category." Sub Indo, Download ".$jenis." ".$category." Subtitle Indo";
        foreach($eps as $eps){
            $embed .=
            '<h3>'.$eps->title.'</h3><p><iframe src="'.route("viewEps",$eps->url).'" width="100%" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>';
        }
        $result = array( "title"=> $title,"tag"=> $tag,"iframe"=> base64_encode($embed));
        return $result;
    }
    function postWeb($site,$drama_id,$header,$body){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $site."/wp-json/wp/v2/posts/".$drama_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic ".$header,
            "Cache-Control: no-cache",
            "Content-Type: application/x-www-form-urlencoded",
        ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);  
        if ($err) {
            return $err;
        } else {
            return $response;
        }    
    }
    private function post_url($url, $body){
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($body != null){
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $headers    = [];
        $headers[]  = 'cache-control: no-cache';
        $headers[]  = 'application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }
    private function link_upload($link, $name = null){
        $links =null;
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $link, $id)) {		
			//$links = "http://cdn.dldramaid.xyz:5000/videos/apis/".$id[1]."/".$name;
			$links = urlencode("https://www.googleapis.com/drive/v3/files/".$id[1]."?alt=media&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M");
		}
		return $links;
    }
    function renameopenload360($id_oload, $name){
		$result_curl= $this->post_url("http://api.openload.co/1/file/rename","login=0c223dba0894ad6a&key=lqTc5EtD&file=".$id_oload."&name=".$name);
		$result = json_decode($result_curl,true);
		return "rename ".$result['msg'];
    }
    function iframesd($url){
        $url_upload = $this->link_upload($url);
        if(!is_null($url_upload)){
            $url_upload= $this->openload360($url_upload);
            return $url_upload;
        }
        return null;
    }
    function renameopenload720($id_oload, $name){
		$result_curl= $this->post_url("http://api.openload.co/1/file/rename","login=1c6d666055b6a4c0&key=t5EK0UYI&file=".$id_oload."&name=".$name);
		$result = json_decode($result_curl,true);
		return "rename ".$result['msg'];
    }
    function iframehd($url){
        $url_upload = $this->link_upload($url);
        if(!is_null($url_upload)){
            $result_id= $this->openload720($url_upload);
            return $result_id;
        }
        return null;
    }
    private function openload720($link){
		$result_curl= $this->post_url("http://api.openload.co/1/remotedl/add","login=1c6d666055b6a4c0&key=t5EK0UYI&url=".$link);
		$result = $this->get_idupload($result_curl);
		return $result;
    }
    private function openload360($link){
		$result_curl= $this->post_url("http://api.openload.co/1/remotedl/add","login=0c223dba0894ad6a&key=lqTc5EtD&url=".$link);
		$result = $this->get_idupload($result_curl);
		return $result;
    }
    private function get_idupload($result){
		$link = json_decode($result,true);
		if ($link['status'] == "200"){
			$result_id="upload_id=".$link["result"]["id"];
		}else{
			$result_id=null;
		}
		return $result_id;
    }
    function check_openload360($url){
        $id_upload = str_replace("upload_id=","",$url);
        $result_curl= $this->post_url("http://api.openload.co/1/remotedl/status","login=0c223dba0894ad6a&key=lqTc5EtD&id=".$id_upload, null);
        $id = json_decode($result_curl,true);	
        if($id["result"]){
            return $id["result"][$id_upload]["extid"];
        }
        return null;
    }
    function checkfile_openload360($url){
        $id_upload = $url;
        $result_curl= $this->post_url("http://api.openload.co/1/file/info","login=0c223dba0894ad6a&key=lqTc5EtD&file=".$id_upload, null);
        $id = json_decode($result_curl,true);	
        if($id["result"][$id_upload]["status"] == "200"){
            return $id["result"][$id_upload]["status"];
        }
        return null;
    }
    function check_openload720($url){
		$id_upload = str_replace("upload_id=","",$url);
		$result_curl= $this->post_url("http://api.openload.co/1/remotedl/status","login=1c6d666055b6a4c0&key=t5EK0UYI&id=".$id_upload, null);
        $id = json_decode($result_curl,true);
        if($id["result"]){
            return $id["result"][$id_upload]["extid"];
        }
        return null;
    }
    function checkfile_openload720($url){
		$id_upload = $url;
		$result_curl= $this->post_url("http://api.openload.co/1/file/info","login=1c6d666055b6a4c0&key=t5EK0UYI&file=".$id_upload, null);
        $id = json_decode($result_curl,true);
        if($id["result"][$id_upload]["status"] == "200"){
            return $id["result"][$id_upload]["status"];
        }
        return null;
    }
    public function refresh_token($token){
		$tokenencode = urlencode($token);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://www.googleapis.com/oauth2/v4/token",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 300,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "client_id=340252279758-6237oibftvlr7523oq2bbbsi67btoe8n.apps.googleusercontent.com&client_secret=9XUUzKJsATodbmpwc2lCTts6&refresh_token=$tokenencode&grant_type=refresh_token",
		  CURLOPT_HTTPHEADER => array(
			"Cache-Control: no-cache",
			"Content-Type: application/x-www-form-urlencoded",
		  ),
        ));
        $response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		    return $err;
		} else {
            return $response;
		}
	}
    function get_token($token){
		if(!Cache::has('token_GD1-'.md5($token))) {
            $checklinkerror['access_token'] = null;
			$result_curl23= $this->refresh_token($token);
			$checklinkerror= json_decode($result_curl23,true);
			if($checklinkerror['access_token']){
                $gmail = Gmail::where('token',$token)->first();
				$get_info23="Bearer ".$checklinkerror['access_token'];
				$expiresAt = now()->addMinutes(50);
                Cache::put('token_GD1-'.md5($token), $get_info23, $expiresAt);
                $gmail->touch();
				return $get_info23;
			}else{
				return $checklinkerror;
			}
        }
        $get_info23 = Cache::get('token_GD1-'.md5($token));
		return $get_info23;
	}
    public function copygd($driveId, $folderid, $title, $token){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/$driveId/copy",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 300,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\"name\":\"$title\",\"parents\":[\"$folderid\"]}",
		  CURLOPT_HTTPHEADER => array(
			"Authorization: ".$this->get_token($token),
			"Cache-Control: no-cache",
			"Content-Type: application/json",
			"Accept: application/json",
		  ),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		  return $err;
		} else {
			if($response){
				$response= json_decode($response,true);
				return $response;
			}
		}

    }
    public function deletegd($id, $token){
        $this->emptytrash($token);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/$id",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 300,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "DELETE",
		  CURLOPT_HTTPHEADER => array(
			"Authorization: ".$this->get_token($token),
			"Cache-Control: no-cache",
			"Content-Type: application/json",
			"Accept: application/json",
		  ),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		  return false;
		} else {
		  $this->emptytrash($token);
		  return true;
		}	
	}
	public function emptytrash($token){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/trash",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 300,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "DELETE",
		  CURLOPT_HTTPHEADER => array(
			"Authorization: ".$this->get_token($token),
			"Cache-Control: no-cache",
			"Content-Type: application/json",
			"Accept: application/json",
		  ),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
		  return $err;
		} else {
		  return $response;
		}	
	}
    function GDCopy($urlVideo, $nameVideo, $kualitas){
        $gmails =  DB::table('gmails')->whereNotIn('token', function($q){
            $q->select('token')->from('mirrors')->groupBy('token')->havingRaw("COUNT(token) >= 500");
        })->inRandomOrder()->first();
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $urlVideo, $id)) {
            $title= $nameVideo.'-'.$kualitas.'.mp4';
            $copyid = $this->copygd($id['1'],$gmails->folderid, $title, $gmails->token);
            if( isset($copyid['id']) ){
                $fieldMirror =  array("id"=>$copyid['id'], "kualitas"=> $kualitas, "url"=>$urlVideo);
                $mirror = new Mirror();
                $mirror->idcopy= $copyid['id'];
                $mirror->kualitas= $kualitas;
                $mirror->token= $gmails->token;
                $mirror->url= $urlVideo;
                $mirror->save();
                return $copyid['id'];
            }else{
                return $copyid;
            }
        }
    }
    function AutoDeleteGd(){
        $mytime = \Carbon\Carbon::now();
        $dt = $mytime->subDays(2);
        $data = Mirror::where("created_at", '<=',$dt )->limit(25)->get();
        foreach($data as $data){
            $idcopy = $data->idcopy;
            $token = $data->token;
            $this->deletegd($idcopy,$token);
            $id = Mirror::where('idcopy',$idcopy);
            $id->delete();
        }
        return $data;
    }
}