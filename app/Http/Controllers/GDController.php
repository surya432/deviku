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
    private function foreachFolder($resultCurl, $tokenDriveAdmin, $id)
    {
        $gmail = \App\gmail::where('tipe', 'master')->first();
        $fdrive = array();
        foreach ($resultCurl['files'] as $Nofiles) {
            $trash = \App\Trash::where("idcopy", $Nofiles['id'])->first();
            if (is_null($trash)) {
                if (preg_match("/720p/", $Nofiles['name'])) {
                    $url = str_replace('-720p.mp4', '', $Nofiles['name']);
                    $content = Content::where('url', $url)->first();
                    if ($content) {
                        // $value = Drama::find($content->drama_id);
                        // if ($content->f720p != "https://drive.google.com/open?id=" . $Nofiles['id']) {
                        $copyID = $this->copygd($Nofiles['id'], $gmail->folderid, $content->url . "-720p", $gmail->token);
                        if (is_null($copyID) || isset($copyID['error'])) {
                            array_push($fdrive, $url . " Error");
                        } else {
                            if (isset($copyID['id'])) {
                                $dataLink = \App\masterlinks::where(["content_id" => $content->id, "kualitas" => "720p"])->get(['id']);
                                if (!is_null($dataLink)) {
                                    \App\masterlinks::destroy($dataLink->toArray());
                                }
                                $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "HD"])->get(['id']);
                                if (!is_null($checkLaporanBroken)) {
                                    \App\masterlinks::destroy($checkLaporanBroken->toArray());
                                }
                                // $content->f720p = "https://drive.google.com/open?id=" . $copyID['id'];
                                // $content->save();
                                $this->changePermission($copyID['id'], $gmail->token);
                                $links = new \App\masterlinks;
                                $links->drive = $copyID['id'];
                                $links->status = "success";
                                $links->kualitas = "720p";
                                $links->apikey = $gmail->token;
                                $links->content_id = $content->id;
                                $links->url = $content->url;
                                $links->save();
                                if ($links) {
                                    $this->addToTrashes($Nofiles['id'], $tokenDriveAdmin);
                                }
                                Drama::find($content->drama_id)->touch();
                                $data = Content::orderBy('id', 'desc')->with('links')->with('backup')->where('drama_id', $id)->get();
                                Cache::forever('Content' . $id, $data);
                                array_push($fdrive, $url . " Update");
                                // $this->addToTrashes($Nofiles['id'], $tokenDriveAdmin);
                            }
                        }
                        // } else if ($content->f720p == "https://drive.google.com/open?id=" . $Nofiles['id']) {
                        //     $this->addToTrashes($Nofiles['id'], $tokenDriveAdmin);
                        // }
                    } else {
                        array_push($fdrive, $url . " Tidak Ditemukan");
                    }
                } elseif (preg_match("/360p/", $Nofiles['name'])) {
                    $url = str_replace('-360p.mp4', '', $Nofiles['name']);
                    $content = Content::where('url', $url)->first();
                    if ($content) {
                        // $value = Drama::find($content->drama_id);
                        // $this->GDMoveFolder($Nofiles['id'], $folderId);
                        // if ($content->f360p != "https://drive.google.com/open?id=" . $Nofiles['id']) {

                        $copyID = $this->copygd($Nofiles['id'], $gmail->folderid, $content->url . "-360p", $gmail->token);
                        if (is_null($copyID) || isset($copyID['error'])) {
                            array_push($fdrive, $url . " Error");
                        } else {
                            if (isset($copyID['id'])) {
                                $dataLink = \App\masterlinks::where(["content_id" => $content->id, "kualitas" => "360p"])->get(['id']);
                                if (!is_null($dataLink)) {
                                    \App\masterlinks::destroy($dataLink->toArray());
                                }
                                $checkLaporanBroken = Brokenlink::where(['contents_id' => $content->id, "kualitas" => "SD"])->get(['id']);
                                if (!is_null($checkLaporanBroken)) {
                                    Brokenlink::destroy($checkLaporanBroken->toArray());
                                }
                                $this->changePermission($copyID['id'], $gmail->token);

                                // $content->f360p = "https://drive.google.com/open?id=" . $copyID['id'];
                                // $content->save();
                                Drama::find($content->drama_id)->touch();
                                $links = new \App\masterlinks;
                                $links->drive = $copyID['id'];
                                $links->status = "success";
                                $links->kualitas = "360p";
                                $links->apikey = $gmail->token;
                                $links->content_id = $content->id;
                                $links->url = $content->url;
                                $links->save();
                                if ($links) {
                                    $this->addToTrashes($Nofiles['id'], $tokenDriveAdmin);
                                }
                                $data = Content::orderBy('id', 'desc')->with('links')->with('backup')->where('drama_id', $id)->get();
                                Cache::forever('Content' . $id, $data);
                                array_push($fdrive, $url . " Update");
                            }
                        }
                        // } else if ($content->f360p == "https://drive.google.com/open?id=" . $Nofiles['id']) {
                        //     $this->changePermission($copyID['id'], $tokenDriveAdmin);
                        //     $this->addToTrashes($Nofiles['id'], $tokenDriveAdmin);
                        // }
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
