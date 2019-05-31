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
        $getlinkproxy = $this->viewsource("https://drive01.herokuapp.com/api/googledrive/" . $idDrive . "?token=ndo&videoName=" . $videoName);
        $result = json_decode($getlinkproxy, true);
        if (!isset($result['data'])) {
            $counts = count($parse['data']);
            if ($counts == 0) {
                return response()->json($result['data'], 404);
            } else {
                foreach ($result['data'] as $a => $b) {
                    if ($b['resolution'] == 360 && $counts == 3) {
                        $this->getLinkAndRedirect($b['src']);
                    } else {
                        if ($b['resolution'] == 480 && $counts == 2) {
                            $this->getLinkAndRedirect($b['src']);
                        } else {
                            return response()->json("Hanya Quality 720p", 404);
                        }
                    }
                }
            }
        }
    }
    function getLinkAndRedirect($links)
    {
        $values = array("rest-drive01.herokuapp.com", "rest-drive03.herokuapp.com", "rest-drive04.herokuapp.com", "rest-drive02.herokuapp.com");
        $result = preg_replace_callback("/rest-drive01.herokuapp.com/", function () use ($values) {
            return $values[array_rand($values)];
        }, $links);
        return Redirect::away($result);
    }
}
