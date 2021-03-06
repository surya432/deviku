<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function GuzzleHttp\json_decode;
use function Matrix\trace;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Brokenlink;
use App\Content;

class ProxyDriveController extends Controller
{
    use HelperController;
    public function index(Request $request)
    {
        $idDrive = $request->input('id');
        $videoName = $request->input('videoName');
        $src = $this->getVideoLinkProxy($idDrive, $videoName);
        return Redirect::away($src);
    }
    public function getVideoLinkProxy($idDrive, $videoName)
    {
        $dataurl = "http://192.241.150.152:5000/api/proxy/" . $idDrive . "?token=ndo&videoName=" . $videoName;
        $getlinkproxy = $this->viewsource($dataurl);
        $result = json_decode($getlinkproxy, true);
        if (isset($result['data'])) {
            $parse1 = $result['data'];
            foreach ($parse1 as $a) {
                if ($a['label'] == '360p') {
                    // return str_replace('https://','http://',$this->getLinkAndRedirect($a['src']));

                    return $a['src'];
                } elseif ($a['label'] == '480p') {
                    // return str_replace('https://', 'http://', $this->getLinkAndRedirect($a['src']));

                    return $a['src'];
                } else {
                    return "http://192.241.150.152:5000/videos/apis/" . $idDrive . "/" . $videoName . ".mp4";
                }
            }
        }
        return "http://192.241.150.152:5000/videos/apis/". $idDrive."/".$videoName.".mp4" ;
    }
    public function getLinkAndRedirect($links)
    {
        //$values = array("http://drive01.herokuapp.com", "http://drive03.herokuapp.com", "http://drive04.herokuapp.com", "http://drive02.herokuapp.com");
        $values = array("192.241.150.152:5000", "192.241.150.152:5000", "192.241.150.152:5000", "192.241.150.152:5000");
        return preg_replace_callback("/192.241.150.152/", function () use ($values) {
            return $values[array_rand($values)];
        }, $links);
    }
    public function getBrokenLink($id)
    {
        $data = DB::table('contents')->whereIn('id', function ($query) {
            $query->from('brokenlinks')->select('contents_id')->get();
        })->where('drama_id', $id)->orderBy('title', 'asc')->get();
        if (!is_null($data)) {
            $returnData = null;
            foreach ($data as $content) {
                if (!$this->CheckHeaderCode($content->f720p)) {
                    $idDrive = $this->GetIdDrive($content->f360p);
                    if ($idDrive) {
                        $returnData .= $this->getVideoLinkProxy($idDrive, $content->url . "-720p") . "\n";
                    }
                }
                if (!$this->CheckHeaderCode($content->f360p)) {
                    $idDrive = $this->GetIdDrive($content->f720p);
                    if ($idDrive) {
                        $returnData .= $this->getVideoLinkProxy($idDrive, $content->url . "-360p") . "\n";
                    }
                }
            }
            return $returnData;
        }
        return response()->json("Nothing Broken link", 404);
    }
    public function fileBrokenLinkPs1()
    {
        $data = DB::table('contents')->whereIn('id', function ($query) {
            $query->from('brokenlinks')->select('contents_id')->get();
        })->where('f720p', 'NOT LIKE', '%picasa%')->orderBy('id', 'desc')->take(10)->get();
        if (!is_null($data)) {
            $urlhost = request()->getHost();
            $returnData = null;
            foreach ($data as $content) {
                if (!$this->CheckHeaderCode($content->f720p) && $this->CheckHeaderCode($content->f360p)) {
                    //untuk brokenlink 720p
                    $idDrive = $this->GetIdDrive($content->f360p);
                    if ($idDrive) {
                        // $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /d "https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($content->f360p) . '&videoName=' . $content->url . '-720p" /a /n ' . " \n";
                        $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /d "' . $this->getVideoLinkProxy($idDrive, $content->url . "-720p") . '" /a /n ' . " \n";
                    }
                } elseif (!$this->CheckHeaderCode($content->f360p) && $this->CheckHeaderCode($content->f720p)) {
                    //untuk brokenlink 350p
                    $idDrive = $this->GetIdDrive($content->f720p);
                    if ($idDrive) {
                        // $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /d "https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($content->f720p) . '&videoName=' . $content->url . '-360p" /a /n ' . " \n";
                        $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /d "' . $this-> getVideoLinkProxy($idDrive, $content->url . "-360p") . '" /a /n ' . " \n";
                    }
                } elseif (!$this->CheckHeaderCode($content->f360p) && !$this->CheckHeaderCode($content->f720p)) {
                    Brokenlink::where("contents_id", $content->id)->delete();
                    $dataContent = Content::find($content->id);
                    $dataContent->f360p = null;
                    $dataContent->f720p = null;
                    $dataContent->save();
                }
            }
            $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /s';
            return $returnData;
        }
    }
    function uploadPhoto(){
        $returnData = null;
        $data = \App\masterlinks::whereNotIn('drive', function ($query) {
            $query->from('mirrorcopies')->where('provider','photogoogle')->select('drive')->get();
        })->where('kualitas','720p')->orderBy('id', 'desc')->take(10)->get();
        if(!is_null($data)){
            foreach ($data as $content) {
                $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /d "http://127.0.0.1:5001/api/googledrive/'.$content->drive.'?token=ndo" /a /n ' . " \n";
            }
        }
        return $returnData;
    }
}
