<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Content;
use App\Mirror;
use DB;
use Cache;
use Jenssegers\Agent\Agent;
use GeoIP;
use App\Brokenlink;

class EmbedController extends Controller
{
    //
    use HelperController;
    function index(Request $request, $url)
    {
        $contentCheck = Content::where('url', $url)->first();
        if (is_null($contentCheck) || is_null($contentCheck->f720p)) {
            return abort(404);
        }
        $agent = new Agent();
        $location = GeoIP::getLocation();
        $country = $location->iso_code;
        if ($country == "KR" || $country == "US" && !$agent->isMobile() || $country == "US" && !$agent->isTablet()) {
            //if($country == "KR" ){
            return abort(404);
        }
        $country = "id";
        $value = $this->MirrorCheck($url);
        return view("embed.index")->with("url", $value)->with('GeoIP', $country);
    }
    function MirrorCheck($url)
    {
        $content = Content::where('url', $url)->first();
        $this->AutoDeleteGd();
        $save = false;
        if (preg_match("/upload_id=/", $content->mirror1)) {
            $resultCheck360 = $this->check_openload360($content->mirror1);
            if (!is_null($resultCheck360) || !empty($resultCheck360) || $resultCheck360 != "") {
                $this->renameopenload360($resultCheck360, $resultCheck360 . "-360p.mp4");
                $save = true;
                $content->mirror1 = $resultCheck360;
            }
        }
        if (is_null($content->mirror1) || $content->mirror1 == "0" || $content->mirror1 == "") {
            $video360p = $content->f360p;
            $openload360 = $this->iframesd($video360p);
            if (!is_null($openload360)) {
                $save = true;
                $content->mirror1 = $openload360;
            }
        } else {
            $openload360 = $this->checkfile_openload360($content->mirror1);
            if (is_null($openload360)) {
                $save = true;
                $content->mirror1 = null;
            }
        }
        if (preg_match("/upload_id=/", $content->mirror3)) {
            $resultCheck720 = $this->check_openload720($content->mirror3);
            if (!is_null($resultCheck720) || !empty($resultCheck720) || $resultCheck720 != "") {
                $this->renameopenload720($resultCheck720, $resultCheck720 . "-720p.mp4");
                $save = true;
                $content->mirror3 = $resultCheck720;
            }
        }
        if (is_null($content->mirror3) || $content->mirror3 == "" || $content->mirror3 == "0") {
            $video720p = $content->f360p;
            $openload720 = $this->iframehd($video720p);
            if (!is_null($openload720)) {
                $save = true;
                $content->mirror3 = $openload720;
            }
        } else {
            $openload720 = $this->checkfile_openload720($content->mirror3);
            if (is_null($openload720)) {
                $save = true;
                $content->mirror3 = null;
            }
        }
        if ($save) {
            $content->save();
        }
        return $content;
    }
    function getDetail(Request $request, $url)
    {
        $content = Content::where('url', $url)->first();
        $linkError = '<div class="spinner"><div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div></div><div id="notif" class="text-center"><p style="color: blue;">Ya Link Sudah Di Rusak!! :( </br> #LaporDenganKomentarDibawah</p></div>';
        switch ($request->input('player')) {
            case 'gd360':
                $f360p = $this->GetIdDrive($content->f360p);
                if ($f360p == '200') {
                    return $this->CopyGoogleDriveID($content->f360p, $url, "SD");
                } else {
                    $checkLaporanBroken = Brokenlink::where('contents_id', $content->id)->first();
                    if (is_null($checkLaporanBroken)) {
                        $laporBrokenLinks = new Brokenlink;
                        $laporBrokenLinks->contents_id = $content->id;
                        $laporBrokenLinks->save();
                    }
                    return '<script type="text/javascript">showPlayer("gd720");</script>';
                }
                break;
            case 'gd720':
                $s720p = $this->GetIdDrive($content->f720p);
                if ($s720p == '200') {
                    //return "helloWorld";
                    return $this->CopyGoogleDriveID($content->f720p, $url, "HD");
                } else {
                    $checkLaporanBroken = Brokenlink::where('contents_id', $content->id)->first();
                    if (is_null($checkLaporanBroken)) {
                        $laporBrokenLinks = new Brokenlink;
                        $laporBrokenLinks->contents_id = $content->id;
                        $laporBrokenLinks->save();
                    }
                    return '<script type="text/javascript">showPlayer("gd360");</script>';
                }
                break;
            case 'mirror1':
                $iframe = "https://oload.stream/embed/" . $content->mirror1;
                return $iframe;
                break;
            case 'mirror2':
                $iframe = "https://www.rapidvideo.com/e/" . $content->mirror2;
                return $iframe;
                break;
            case 'mirror3':
                $iframe = "https://oload.stream/embed/" . $content->mirror3;
                return $iframe;
                break;
            case "download_links":
                $returncontent = "";
                $returncontent .= '<div id="notif" class="text-center"><p style="color: blue;">';
                if (!is_null($content->mirror1)) {
                    if (!preg_match("/upload_id=/", $content->mirror1)) {
                        $returncontent .= "<a href='https://oload.stream/f/" . $content->mirror1 . "' class='btn btn-sm btn-primary' target='_blank'>Openload 360p</a>";
                    }
                }
                if (!is_null($content->mirror3)) {
                    if (!preg_match("/upload_id=/", $content->mirror3)) {
                        $returncontent .= "<a href='https://oload.stream/f/" . $content->mirror3 . "' class='btn btn-sm btn-primary' target='_blank'>Openload 720p</a>";
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
    function CopyGoogleDriveID($urlDrive, $url, $kualitas)
    {
        $mytime = \Carbon\Carbon::now();
        $mirror = Mirror::select('idcopy')->where('url', $urlDrive)->where('kualitas', $kualitas)->first();
        //return json_encode($mirror);
        if (is_null($mirror)) {
            $copyID = $this->GDCopy($urlDrive, md5($url . $mytime), $kualitas);
            if (is_null($copyID) || isset($copyID['error'])) {
                return abort(404);
            };
            return $this->GetPlayer($copyID);
        } else {
            return $this->GetPlayer($mirror->idcopy);
        }
    }
    function GetPlayer($urlDrive)
    {
        return file_get_contents("http://player.nontonindramaonline.com/json.php?url=https://drive.google.com/open?id=" . $urlDrive);
    }
    function deletegdbydate()
    {
        return $this->AutoDeleteGd();
    }
}