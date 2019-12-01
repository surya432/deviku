<?php

namespace App\Http\Controllers;

use App\Brokenlink;
use App\Content;
use App\Drama;
use App\Setting;
use Cache;
use Illuminate\Http\Request;

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
        $resultCurl['files'] = null;
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
                    $value = Drama::find($content->drama_id);
                    if ($value) {
                        $folderId = $value->folderid;
                    } else {
                        $folderId = $oldFolder;
                    }
                    $this->GDMoveFolder($Nofiles['id'], $folderId);
                    if ($content->f720p != "https://drive.google.com/open?id=" . $Nofiles['id']) {
                        $this->addToTrashes($this->GetIdDrive($content->f720p), $tokenDriveAdmin);
                        $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->first();
                        if (!is_null($checkLaporanBroken)) {
                            Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->delete();
                        }
                        $content->f720p = "https://drive.google.com/open?id=" . $Nofiles['id'];
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
                    $value = Drama::find($content->drama_id);
                    if ($value) {
                        $folderId = $value->folderid;
                    } else {
                        $folderId = $oldFolder;
                    }
                    $this->GDMoveFolder($Nofiles['id'], $folderId);
                    if ($content->f360p != "https://drive.google.com/open?id=" . $Nofiles['id']) {
                        $this->addToTrashes($this->GetIdDrive($content->f360p), $tokenDriveAdmin);
                        $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "SD"])->first();
                        if (!is_null($checkLaporanBroken)) {
                            Brokenlink::where(['contents_id' => $content->id, "kualitas" => "SD"])->delete();
                        }
                        $content->f360p = "https://drive.google.com/open?id=" . $Nofiles['id'];
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
    private function foreachFolder($resultCurl, $tokenDriveAdmin, $id)
    {
        $gmail = \App\gmail::where('tipe', 'master')->first();
        $fdrive = array();
        foreach ($resultCurl['files'] as $Nofiles) {
            $trash = \App\Trash::where("idcopy", $Nofiles['id'])->first();
            if (is_null($trash)) {
                if (preg_match("/-720p.mp4/", $Nofiles['name'])) {
                    $url = str_replace('-720p.mp4', '', $Nofiles['name']);
                    $content = Content::where('url', $url)->first();
                    if ($content) {
                        $value = Drama::find($content->drama_id);
                        if ($content->f720p != "https://drive.google.com/open?id=" . $Nofiles['id']) {
                            $copyID = $this->copygd($Nofiles['id'], $gmail->folderid, $content->url . "-720p", $gmail->token);
                            if (is_null($copyID) || isset($copyID['error'])) {
                                array_push($fdrive, $url . " Error");
                            } else {
                                if (isset($copyID['id'])) {
                                    $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->first();
                                    if (!is_null($checkLaporanBroken)) {
                                        Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->delete();
                                    }
                                    $content->f720p = "https://drive.google.com/open?id=" . $copyID['id'];
                                    $content->save();
                                    Drama::find($content->drama_id)->touch();
                                    $data = Content::orderBy('id', 'desc')->where('drama_id', $id)->get();
                                    Cache::forever('Content' . $id, $data);
                                    array_push($fdrive, $url . " Update");
                                }
                            }
                            $this->addToTrashes($Nofiles['id'], $tokenDriveAdmin);
                        }
                    } else {
                        array_push($fdrive, $url . " Tidak Ditemukan");
                    }
                } elseif (preg_match("/-360p.mp4/", $Nofiles['name'])) {
                    $url = str_replace('-360p.mp4', '', $Nofiles['name']);
                    $content = Content::where('url', $url)->first();
                    if ($content) {
                        $value = Drama::find($content->drama_id);
                        // $this->GDMoveFolder($Nofiles['id'], $folderId);
                        if ($content->f360p != "https://drive.google.com/open?id=" . $Nofiles['id']) {
                            $copyID = $this->copygd($Nofiles['id'], $gmail->folderid, $content->url . "-360p", $gmail->token);
                            if (is_null($copyID) || isset($copyID['error'])) {
                                array_push($fdrive, $url . " Error");
                            } else {
                                if (isset($copyID['id'])) {
                                    $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->first();
                                    if (!is_null($checkLaporanBroken)) {
                                        Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->delete();
                                    }
                                    $content->f360p = "https://drive.google.com/open?id=" . $copyID['id'];
                                    $content->save();
                                    Drama::find($content->drama_id)->touch();
                                    $data = Content::orderBy('id', 'desc')->where('drama_id', $id)->get();
                                    Cache::forever('Content' . $id, $data);
                                    array_push($fdrive, $url . " Update");
                                }
                            }
                            $this->addToTrashes($Nofiles['id'], $tokenDriveAdmin);
                        }
                    } else {
                        array_push($fdrive, $url . " Tidak Ditemukan");
                    }
                }
            }
        }
        return $fdrive;
    }
    public function syncFolder($id)
    {
        $gmail = \App\gmail::where('tipe', 'upload')->first();

        if ($id == 0) {
            $resultCurl = $this->singkronfile($gmail->folderid);
        } else {
            $drama = \App\Drama::where('id', $id)->first();
            $resultCurl = $this->singkronfile($drama->folderid);
        }
        return view('dashboard.singkronContent')->with('url', $this->foreachFolder($resultCurl, $gmail->token, $id));

    }
    public function createFolderDrive(Request $request)
    {
        $dataType = Drama::find($request->input('id'));
        $folderName = $dataType->title . " [$dataType->id]";
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
