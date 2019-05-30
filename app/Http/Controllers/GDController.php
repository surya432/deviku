<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use App\Drama;
use App\Content;
use Cache;
use App\Setting;
use App\Type;
use App\Trash;
use App\Brokenlink;
use Yajra\DataTables\Facades\DataTables;

class GDController extends Controller
{
    //

    use HelperController;
    public function AdminToken()
    {
        $settingData = Setting::find(1);
        $tokenDriveAdmin = $settingData->tokenDriveAdmin;
        $resultCurl = $this->get_token($tokenDriveAdmin);

        return $resultCurl;
    }
    public function singkronFolder()
    {
        $resultCurl['files']  = null;
        $settingData = Setting::find(1);
        $oldFolder = $settingData->folder720p;
        $resultCurl = $this->singkronfile($oldFolder);
        $fdrive = array();
        foreach ($resultCurl['files'] as $Nofiles) {
            if (preg_match('/[[\d]+]/', $Nofiles['name'], $output_array)) {
                $url = str_replace(array('[', ']'), '', $output_array[0]);
                $content = Drama::where('id', $url)->first();
                if ($content) {
                    if ($content->folderid == "a" || $content->folderid != $Nofiles['id']) {
                        $content->folderid = $Nofiles['id'];
                        array_push($fdrive, $content->title);
                    }
                    $content->save();
                }
            }
        }
        $value = Drama::with('country')->with('type')->with('eps')->orderBy('id', 'desc')->get();
        Cache::forever('Drama', $value);
        //return dd($fdrive);
        return view('dashboard.singkronContent')->with('url', $fdrive);
    }
    function addToTrashes($idcopy, $token)
    {
        try {
            $trashes = new Trash();
            $trashes->idcopy = $idcopy;
            $trashes->token = $token;
            $trashes->save();
        } catch (Exception $e) {
            echo $e->errorMessage();
        }
    }

    public function singkron($id)
    {
        $settingData = Setting::find(1);
        $tokenDriveAdmin = $settingData->tokenDriveAdmin;

        if ($id == "0") {
            $oldFolder = $settingData->folderUpload;
            $resultCurl = $this->singkronfile($oldFolder);
        } else {
            $settingData = Drama::find($id);
            $oldFolder = $settingData->folderid;
            $resultCurl = $this->singkronfile($oldFolder);
        }
        $fdrive = array();
        foreach ($resultCurl['files'] as $Nofiles) {
            if (preg_match("/-720p.mp4/", $Nofiles['name'])) {
                $url = str_replace('-720p.mp4', '', $Nofiles['name']);
                $content = Content::where('url', $url)->first();
                if ($content) {
                    $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->first();
                    if (!is_null($checkLaporanBroken)) {
                        Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->delete();
                        if ($content->f720p != $content->f360p) {
                            $trashes = new Trash();
                            $trashes->idcopy =$this->GetIdDrive($content->f720p);
                            $trashes->token = $tokenDriveAdmin;
                            $trashes->save();
                        }
                    }

                    $value = Drama::with('country')->with('type')->with('eps')->orderBy('id', 'desc')->where('dramas.id', $content->drama_id)->first();
                    if ($value) {
                        $folderId = $value->folderid;
                    } else {
                        //$folderId = $id;
                        $folderId = $oldFolder;
                    }
                    $this->GDMoveFolder($Nofiles['id'], $folderId);
                    if ($content->f720p != "https://drive.google.com/open?id=" . $Nofiles['id']) {

                        if (!is_null($content->f720p)) {
                            $this->addToTrashes($this->GetIdDrive($content->f720p), $tokenDriveAdmin);
                        }
                        $content->f720p = "https://drive.google.com/open?id=" .  $Nofiles['id'];
                        if (is_null($content->f360p)) {
                            $content->f360p = "https://drive.google.com/open?id=" . $Nofiles['id'];
                        }
                        $content->save();
                        Drama::find($content->drama_id)->touch();
                        $data = Content::orderBy('id', 'desc')->where('drama_id', $id)->get();
                        Cache::forever('Content' . $id, $data);
                        array_push($fdrive, $url);
                    }
                }
            } elseif (preg_match("/-360p.mp4/", $Nofiles['name'])) {
                $url = str_replace('-360p.mp4', '', $Nofiles['name']);
                $content = Content::where('url', $url)->first();
                if ($content) {
                    $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "SD"])->first();
                    if (!is_null($checkLaporanBroken)) {
                        Brokenlink::where(['contents_id' => $content->id, "kualitas" => "SD"])->delete();
                        if ($content->f720p != $content->f360p) {
                            $trashes = new Trash();
                            $trashes->idcopy =$this->GetIdDrive($content->f360p);
                            $trashes->token = $tokenDriveAdmin;
                            $trashes->save();
                        }
                    }
                    $value = Drama::with('country')->with('type')->with('eps')->orderBy('id', 'desc')->where('dramas.id', $content->drama_id)->first();
                    if ($value) {
                        $folderId = $value->folderid;
                    } else {
                        $folderId = $oldFolder;
                    }
                    $this->GDMoveFolder($Nofiles['id'], $folderId);
                    if ($content->f360p != "https://drive.google.com/open?id=" . $Nofiles['id']) {

                        if (!is_null($content->f360p)) {
                            $this->addToTrashes($this->GetIdDrive($content->f360p), $tokenDriveAdmin);
                        }
                        $content->f360p = "https://drive.google.com/open?id=" . $Nofiles['id'];
                        if (is_null($content->f720p)) {
                            $content->f720p = "https://drive.google.com/open?id=" . $Nofiles['id'];
                        }
                        $content->save();
                        Drama::find($content->drama_id)->touch();
                        $data = Content::orderBy('id', 'desc')->where('drama_id', $id)->get();
                        Cache::forever('Content' . $id, $data);
                        array_push($fdrive, $url);
                    }
                }
            }
        }
        return view('dashboard.singkronContent')->with('url', $fdrive);
    }
    function createFolderDrive(Request $request)
    {
        $dataType = Drama::find($request->input('id'));
        $folderName =  $dataType->title . " [$dataType->id]";
        $resultCurl = $this->GDCreateFolder($folderName);
        if (isset($resultCurl['id'])) {
            $dataType = Drama::find($dataType->id);
            if ($dataType) {
                $dataType->folderid = $resultCurl['id'];
                $dataType->save();
            }
            $dataTypeasd = "Insert Success";
            return response()->json($dataTypeasd, 201);
        } else {
            return response()->json($resultCurl, 201);
        }
    }
}
