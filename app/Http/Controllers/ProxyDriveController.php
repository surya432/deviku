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
    function index(Request $request)
    {
        $idDrive = $request->input('id');
        $videoName = $request->input('videoName');
		$src = str_replace('https://','http://',$this->getVideoLinkProxy($idDrive, $videoName));
        return Redirect::away($src);
    }
    function getVideoLinkProxy($idDrive, $videoName)
    {
        $getlinkproxy = $this->viewsource("http://192.241.150.152:5000/api/proxy/" . $idDrive . "?token=ndo&videoName=" . $videoName);
        $result = json_decode($getlinkproxy, true);
        if (isset($result['data'])) {
            $parse1 = $result['data'];
            foreach ($parse1 as $a) {
                if ($a['label'] == '360p') {
                    return $this->getLinkAndRedirect($a['src']);
                } else if ($a['label'] == '480p') {
                    return $this->getLinkAndRedirect($a['src']);
                } else {
                    return "https://www.googleapis.com/drive/v3/files/" . $idDrive . "?alt=media&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&name=" . $videoName . "-720p.mp4";
                }
            }
        }
        return response()->json($result, 404);
    }
    function getLinkAndRedirect($links)
    {
        //$values = array("http://drive01.herokuapp.com", "http://drive03.herokuapp.com", "http://drive04.herokuapp.com", "http://drive02.herokuapp.com");
        $values = array("localhost:5000", "localhost:5000", "localhost:5000", "localhost:5000");
		return preg_replace_callback("/drive01.herokuapp.com/", function () use ($values) {
            return $values[array_rand($values)];
        }, $links);
    }
    function getBrokenLink($id)
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
    function fileBrokenLinkPs1()
    {
        $data = DB::table('contents')->whereIn('id', function ($query) {
            $query->from('brokenlinks')->select('contents_id')->get();
        })->where('f720p', 'NOT LIKE', '%picasa%')->orderBy('id','desc')->take(10)->get();
        if (!is_null($data)) {
            $urlhost = request()->getHost();
            $returnData = null;
            foreach ($data as $content) {
                if (!$this->CheckHeaderCode($content->f720p) && $this->CheckHeaderCode($content->f360p)) {
                    //untuk brokenlink 720p
                    $idDrive = $this->GetIdDrive($content->f360p);
                    if ($idDrive) {
                        $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /d "https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($content->f360p) . '&videoName=' . $content->url . '-720p" /a /n ' . " \n";
                    }
                } else if (!$this->CheckHeaderCode($content->f360p) && $this->CheckHeaderCode($content->f720p)) {
                    //untuk brokenlink 350p
                    $idDrive = $this->GetIdDrive($content->f720p);
                    if ($idDrive) {
                        $returnData .= '"C:\Program Files (x86)\Internet Download Manager\IDMan.exe" /d "https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($content->f720p) . '&videoName=' . $content->url . '-360p" /a /n ' . " \n";
                    }
                } else if (!$this->CheckHeaderCode($content->f360p) && !$this->CheckHeaderCode($content->f720p)) {
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
}
