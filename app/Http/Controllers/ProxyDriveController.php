<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function GuzzleHttp\json_decode;
use function Matrix\trace;
use Illuminate\Support\Facades\Redirect;

class ProxyDriveController extends Controller
{
    //
    use HelperController;
    function index(Request $request)
    {
        $idDrive = $request->input('id');
        $videoName = $request->input('videoName');
        $getlinkproxy = $this->viewsource("https://drive01.herokuapp.com/api/proxy/" . $idDrive . "?token=ndo&videoName=" . $videoName);
        $result = json_decode($getlinkproxy, true);
        if (isset($result['data'])) {
            $counts = count($result['data']);
            if ($counts == 0) {
                return response()->json($result['reason'], 404);
            } else {
                $parse1 = $result['data'];
                foreach ($parse1 as $a) {
                    if ($a['label'] == '360p') {
                        return Redirect::away($this->getLinkAndRedirect($a['src']));
                    } else {
                        if ($a['label'] == '480p') {
                            return Redirect::away($this->getLinkAndRedirect($a['src']));
                        } else {
                            return Redirect::away("https://www.googleapis.com/drive/v3/files/" . $idDrive . "?alt=media&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&name=" . $videoName . "-720p.mp4");
                        }
                    }
                }
            }
        } else {
            return response()->json($result, 404);
        }
    }
    function getLinkAndRedirect($links)
    {
        $values = array("rest-drive01.herokuapp.com", "rest-drive03.herokuapp.com", "rest-drive04.herokuapp.com", "rest-drive02.herokuapp.com");
        return $result = preg_replace_callback("/rest-drive01.herokuapp.com/", function () use ($values) {
            return $values[array_rand($values)];
        }, $links);
    }
}
