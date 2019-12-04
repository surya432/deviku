<?php

namespace App\Http\Controllers;

use App\Brokenlink;
use App\Classes\FEmbed as FEmbed;
use App\Classes\RapidVideo as RapidVideo;
use App\Content;
use App\Mirror;
use App\Setting;
use App\Trash;
use Cache;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class EmbedController extends Controller
{
    use HelperController;
    public function index(Request $request, $url)
    {
        $url = Content::where('url', $url)->first();
        if (is_null($url)) {
            return abort(404);
        }
        $environment = app()->environment();
        if ($environment != "local") {
            $agent = new Agent();
            $location = \GeoIP::getLocation();
            $country = $location->iso_code;
            if ($country == "KR" || $country == "US" && !$agent->isMobile() || $country == "US" && !$agent->isTablet()) {
                //if($country == "KR" ){
                return abort(404);
            }
        } else {
            $country = "id";
        }
        // $url = $this->MirrorCheck($url);
        // $pad_code = Cache::remember('23132popads', "3600", function () {
        //     $pad = new \App\Classes\PopAdsAdcode();
        //     return $pad->read();
        // });
        // $pad_code = "";
        if (isset($url['f720p'])) {
            $fembed = $this->getMirror($url['f720p'], "fembed.com");
            $rapidvideo = $this->getMirror($url['f720p'], "rapidvideo.com");
            $openload = $this->getMirror($url['f720p'], "openload.com");
        }
        $setting = Setting::find(1);

        $pad_code = Cache::remember('PopAdsAdcode', "06", function () {
            $pad = new \App\Classes\PopAdsAdcode();
            $pad_code = $pad->read();
            return $pad_code;
        });
        return view("embed.index", compact("url", "country", "setting", "fembed", "pad_code", "rapidvideo", "openload"));
    }
    public function addToTrashes()
    {
        $dayFiles = Setting::find(1)->dayFiles;
        $mytime = \Carbon\Carbon::now();
        $dt = $mytime->subDays($dayFiles);
        $datas = Mirror::where("created_at", '<=', date_format($dt, "Y/m/d H:i:s"))->take(20)->get();
        if ($datas) {
            foreach ($datas as $datass) {
                $trashes = new Trash();
                $trashes->idcopy = $datass->idcopy;
                $trashes->token = $datass->token;
                $trashes->save();
                Mirror::where('idcopy', $datass->idcopy)->delete();
            }
        }
    }
    public function MethodBrokenlinks($id, $kualitas, $options)
    {
        $seconds = 1000 * 60 * 4;
        Cache::remember('MethodBrokenlinks', $seconds, function () use ($id, $kualitas, $options) {
            $checkLaporanBroken = Brokenlink::where(['contents_id' => $id, "kualitas" => $kualitas])->first();
            if (!is_null($checkLaporanBroken) && $options == "delete") {
                Brokenlink::where(['contents_id' => $id, "kualitas" => $kualitas])->delete();
            } elseif (is_null($checkLaporanBroken) && $options == "add") {
                $laporBrokenLinks = new Brokenlink;
                $laporBrokenLinks->contents_id = $id;
                $laporBrokenLinks->kualitas = $kualitas;
                $laporBrokenLinks->save();
            }
        });
    }
    public function getDetail(Request $request, $url)
    {
        $content = Content::where('url', $url)->first();
        $this->addToTrashes();
        $linkError = '<div class="spinner"><div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div></div><div id="notif" class="text-center"><p style="color: blue;">Ya Link Sudah Di Rusak!! Coba Server Lain Kak. :( </br> #LaporDenganKomentarDibawah</p></div>';
        switch ($request->input('player')) {
            case 'gd360':
                $f360p = $this->CheckHeaderCode($content->f360p);
                if ($f360p) {
                    $this->MethodBrokenlinks($content->id, "SD", "delete");
                    return $this->CopyGoogleDriveID($content->f360p, $url, "SD");
                } else {
                    $this->MethodBrokenlinks($content->id, "SD", "add");
                    return '<script type="text/javascript">showPlayer("gd720");</script>';
                }
                break;
            case 'gd720':
                $s720p = $this->CheckHeaderCode($content->f720p);
                if ($s720p) {
                    $this->MethodBrokenlinks($content->id, "HD", "delete");
                    return $this->CopyGoogleDriveID($content->f720p, $url, "HD");
                } else {
                    $this->MethodBrokenlinks($content->id, "HD", "add");
                    return $this->GetPlayer("1av4t26HaqPqgSlBAj6D_FSO54RyZR2Tu");
                }
                break;
            case 'mirror1':
                $iframe = $this->getMirror($content->f720p, "fembed.com");
                return $iframe;
                break;
            case 'mirror2':
                $iframe = $this->getMirror($content->f720p, "rapidvideo.com");
                return $iframe;
                break;
            case 'mirror3':
                $iframe = $this->getMirror($content->f720p, "openload.com");
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
                if (!is_null($content->mirror2)) {
                    if (!preg_match("/upload_id=/", $content->mirror2)) {
                        $returncontent .= "<a href='http://www.rapidvideo.com/d/" . $content->mirror2 . "' class='btn btn-sm btn-primary' target='_blank'>RapidVideo 720p</a>";
                    }
                }
                $returncontent .= '</p></div>';
                return $returncontent;
                break;
        }
    }
    public function CopyGoogleDriveID($urlDrive, $url, $kualitas)
    {
        $linkError = '<div class="spinner"><div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div></div><div id="notif" class="text-center"><p style="color: blue;">Gagal Getlink video!! :( </br> #PERLU REFRESH</p></div>';
        $mytime = \Carbon\Carbon::now();
        $mirror = Mirror::select('idcopy')->where('url', $urlDrive)->where('kualitas', $kualitas)->first();
        //return json_encode($mirror);
        if (is_null($mirror)) {
            $copyID = $this->GDCopy($urlDrive, md5($url . $mytime), $kualitas);
            if (is_null($copyID) || isset($copyID['error'])) {
                return $this->GetPlayer("1av4t26HaqPqgSlBAj6D_FSO54RyZR2Tu");
            };
            return $this->GetPlayer($copyID);
        } else {
            return $this->GetPlayer($mirror->idcopy);
        }
    }
    public function GetPlayer($urlDrive)
    {
        //  return ;
        return url('/') . "/embed.php?id=" . $this->my_simple_crypt($urlDrive);
    }
    public function my_simple_crypt($string, $action = 'e')
    {
        $secret_key = 'GReg7rNx2z[2';
        $secret_iv = 'C0?s9rh4';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'e') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else if ($action == 'd') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
    public function getMirror($data, $mirror)
    {
        switch ($mirror) {
            case "fembed.com":
                try {
                    return $this->fembedCopy($data, $mirror);
                } catch (Exception $e) {
                    return null;
                }
                break;
            case "rapidvideo.com":
                try {
                    return $this->RapidVideo($data, $mirror);
                } catch (Exception $e) {
                    return null;
                }
                break;
            case "openload.com":
                try {
                    return $this->openload($data, $mirror);
                } catch (Exception $e) {
                    return null;
                }
                break;
            case "drive.google.com":
                try {
                    return $this->googledrive($data, $mirror);
                } catch (Exception $e) {
                    return null;
                }
                break;
        }
    }
    public function getProviderStatus($data, $mirror)
    {
        return \App\mirrorkey::join('master_mirrors', 'master_mirrors.id', '=', 'mirrorkeys.master_mirror_id')->where(['master_mirrors.name' => $mirror])->inRandomOrder()->first();
    }
    public function GetIdDrive($urlVideoDrive)
    {
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $urlVideoDrive, $id)) {
            return $id[1];
        } else {
            return false;
        }
    }
    public function syncFembed($data, $mirror)
    {
        $fembed = new FEmbed();
        $apikey = $fembed->getKey($this->getProviderStatus($data, $mirror), $mirror);
        $dataCurl = $fembed->fembedCheck($apikey);
        if ($dataCurl['success']) {
            $arrayid = array();
            foreach ($dataCurl['data'] as $a => $b) {
                $apikeys = $apikey . "&task_id=" . $b['id'];
                $dataMirror = \App\Mirrorcopy::where('apikey', $apikeys)->first();
                if ($b['status'] == 'Task is completed') {
                    if ($dataMirror) {
                        $dataMirror->url = $b['file_id'];
                        $dataMirror->status = $b['status'];
                        $dataMirror->save();
                        array_push($arrayid, $b['id']);
                    }
                } elseif ($b['status'] == "Could not connect to download server") {
                    array_push($arrayid, $b['id']);
                    if ($dataMirror) {
                        $dataMirror->delete();
                    }
                } elseif ($b['status'] == "Not an allowed video file") {
                    array_push($arrayid, $b['id']);
                    if ($dataMirror) {
                        $dataMirror->delete();
                    }
                } elseif ($b['status'] == "Timed out") {
                    array_push($arrayid, $b['id']);
                    if ($dataMirror) {
                        $dataMirror->delete();
                    }
                } elseif ($b['status'] == "could not connect to server") {
                    array_push($arrayid, $b['id']);
                    if ($dataMirror) {
                        $dataMirror->delete();
                    }
                } elseif ($b['status'] == "could not verify file to download") {
                    array_push($arrayid, $b['id']);
                    if ($dataMirror) {
                        $dataMirror->delete();
                    }
                } elseif ($b['status'] == "file is too small, minimum allow size is 10,240 bytes") {
                    array_push($arrayid, $b['id']);
                    if ($dataMirror) {
                        $dataMirror->delete();
                    }
                }
            }
            if (!empty($arrayid)) {
                $apikeyremove = $apikey . "&remove_ids=" . json_encode($arrayid);
                $dataCurl = $fembed->fembedCheck($apikeyremove);
            }

        }
    }
    public function fembedCopy($data, $mirror)
    {
        $response = [];
        $this->syncFembed($data, $mirror);
        $ClientID = $this->getProviderStatus($data, $mirror);
        if ($ClientID != null) {
            $fembed = new FEmbed();
            $copies = \App\Mirrorcopy::where(['drive' => $data])->where(['provider' => $mirror])->first();
            if ($copies) {
                $url = null;
                if ($copies['status'] == "Task is completed") {
                    Cache::remember(md5($copies['url']), 3600 * 48, function () use ($data, $mirror, $fembed, $copies) {
                        $keys = $fembed->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&file_id=" . $copies['url'];
                        $dataCheck = $fembed->fembedFile($keys);
                        if ($dataCheck['data']['status'] != 'Live') {
                            $copies->delete();
                        }
                    });
                    return "https://www.fembed.com/v/" . $copies['url'];
                }
                return $url;
            } else {
                // if ($ClientID['status'] == "Up") {
                //     $urlDownload = [];
                //     $nameVideo = md5($data);
                //     $driveId = $this->GetIdDrive($data);
                //     $severDownload = $this->getProviderStatus($data, "ServerDownload");
                //     $urlDownload[] = array("link" => $severDownload['keys'] . "/" . $driveId . "/" . $nameVideo . ".mp4", "headers" => "");
                //     $datacurl = $fembed->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&links=" . json_encode($urlDownload);
                //     $resultCurl = $fembed->fembedUpload($datacurl);
                //     if ($resultCurl['success']) {
                //         $mirrorcopies = new \App\Mirrorcopy();
                //         $mirrorcopies->url = null;
                //         $mirrorcopies->status = "uploaded";
                //         $mirrorcopies->drive = $data;
                //         $mirrorcopies->provider = $mirror;
                //         $mirrorcopies->apikey = $fembed->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&task_id=" . $resultCurl['data'][0];
                //         $mirrorcopies->save();
                //         return "";
                //     } else {
                //         return "";
                //     }
                // } else {
                //     return "";
                // }
                return "";

            }
        } else {
            return "";
        }
    }

    public function RapidVideo($data, $mirror)
    {
        $ClientID = $this->getProviderStatus($data, $mirror);
        if (is_null($ClientID)) {
            return "";
        } else {
            $rapidvideo = new \App\Classes\RapidVideo();
            $copies = \App\Mirrorcopy::where(['drive' => $data])->where(['provider' => $mirror])->first();
            if (is_null($copies)) {
                // if ($ClientID['status'] == "Up") {
                //     $nameVideo = md5($data);
                //     $driveId = $this->GetIdDrive($data);
                //     $severDownload = $this->getProviderStatus($data, "ServerDownload");
                //     $urlDownload = $severDownload['keys'] . "/" . $driveId . "/" . $nameVideo . ".mp4";
                //     $datacurl = $rapidvideo->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&url=" . $urlDownload;
                //     $resultCurl = $rapidvideo->RapidVideoUpload($datacurl);
                //     if ($resultCurl['status'] == "OK") {
                //         $mirrorcopies = new \App\Mirrorcopy();
                //         $mirrorcopies->url = null;
                //         $mirrorcopies->status = "uploaded";
                //         $mirrorcopies->drive = $data;
                //         $mirrorcopies->provider = $mirror;
                //         $mirrorcopies->apikey = $rapidvideo->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&id=" . $resultCurl['id'];
                //         $mirrorcopies->save();
                //     }
                //     return "";
                // }
                return "";
            } else {
                $urlID = "";
                if ($copies['status'] == "uploaded") {
                    $checkResult = $rapidvideo->RapidVideoStatus($copies['apikey']);
                    if ($checkResult['msg'] == "OK") {
                        foreach ($checkResult['result'] as $a => $b) {
                            if ($b['status'] == 'finished') {
                                $copies->url = $b['extid'];
                                $copies->status = "Task is completed";
                                $copies->save();
                                $urlID = "https://www.rapidvideo.com/v/" . $b['extid'];
                            }
                        }
                    }
                    return $urlID;
                }
                return "https://www.rapidvideo.com/v/" . $copies['url'];
            }
        }
    }
    public function openload($data, $mirror)
    {
        $ClientID = $this->getProviderStatus($data, $mirror);
        if (is_null($ClientID)) {
            return "";
        } else {
            $openload = new \App\Classes\Openload();
            $copies = \App\Mirrorcopy::where(['drive' => $data])->where(['provider' => $mirror])->first();
            if (is_null($copies)) {
                if ($ClientID['status'] != "Up") {
                    return "";
                }
                // $nameVideo = md5($data);
                // $driveId = $this->GetIdDrive($data);
                // $severDownload = $this->getProviderStatus($data, "ServerDownload");
                // $urlDownload = $severDownload['keys'] . "/" . $driveId . "/" . $nameVideo . ".mp4";
                // $datacurl = $openload->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&url=" . $urlDownload;
                // $resultCurl = $openload->OpenloadUpload($datacurl);
                // if ($resultCurl['msg'] != "OK") {
                //     return "";
                // }
                // $mirrorcopies = new \App\Mirrorcopy();
                // $mirrorcopies->url = null;
                // $mirrorcopies->status = "uploaded";
                // $mirrorcopies->drive = $data;
                // $mirrorcopies->provider = $mirror;
                // $mirrorcopies->apikey = $openload->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&id=" . $resultCurl['id'];
                // $mirrorcopies->save();
                return "";
            } else {
                $urlID = "";
                if ($copies['status'] == "uploaded") {
                    $checkResult = $openload->OpenloadStatus($copies['apikey']);
                    if ($checkResult['msg'] == "OK") {
                        foreach ($checkResult['result'] as $a => $b) {
                            if ($b['status'] == 'finished') {
                                $copies->url = $b['extid'];
                                $copies->status = "Task is completed";
                                $copies->save();
                                $keys = $openload->getKey($this->getProviderStatus($data, $mirror), $mirror) . "&id=" . $b['extid'];
                                if ($copies['apikey'] == $keys) {
                                    $urlID = "http://oload.stream/f/" . $b['extid'];
                                }
                            }
                        }
                    }
                    return $urlID;
                }
                return "https://oload.stream/f/" . $copies['url'];
            }
        }
    }
    public function googledrive($data, $mirror)
    {
        $ClientID = $this->getProviderStatus($data, $mirror);
        if (is_null($ClientID)) {
            return "";
        } else {
            $googledrive = new \App\Classes\GoogleDriveAPIS();
            $copies = \App\Mirrorcopy::where(['drive' => $data])->where(['provider' => $mirror])->first();
            if (!is_null($copies)) {
                return $this->GetPlayer($copies['url']);
            } else {
                if ($ClientID['status'] != "Up") {
                    return null;
                }
                $keys = $this->getProviderStatus($data, $mirror);
                $driveId = $this->GetIdDrive($data);
                $copyID = $googledrive->GDCopy($driveId, $keys);
                if (is_null($copyID) || isset($copyID['error'])) {
                    return "";
                };
                $mirrorcopies = new \App\Mirrorcopy();
                $mirrorcopies->url = $copyID;
                $mirrorcopies->status = "Task is completed";
                $mirrorcopies->drive = $data;
                $mirrorcopies->provider = $mirror;
                $mirrorcopies->apikey = $keys['keys'];
                $mirrorcopies->save();
                return $this->GetPlayer($copyID);
            }
        }
    }
    public function googledriveBackup($data, $mirror)
    {
        $ClientID = $this->getProviderStatus($data, $mirror);
        if (is_null($ClientID)) {
            return "";
        } else {
            $googledrive = new \App\Classes\GoogleDriveAPIS();
            $copies = \App\Mirrorcopy::where(['drive' => $data])->where(['provider' => $mirror])->first();
            if (!is_null($copies)) {
                return false;
            } else {
                if ($ClientID['status'] != "Up") {
                    return false;
                }
                $keys = $this->getProviderStatus($data, $mirror);
                $driveId = $this->GetIdDrive($data);
                $copyID = $googledrive->GDCopy($driveId, $keys);
                if (is_null($copyID) || isset($copyID['error'])) {
                    return false;
                };
                $mirrorcopies = new \App\Mirrorcopy();
                $mirrorcopies->url = $copyID;
                $mirrorcopies->status = "Task is Completed";
                $mirrorcopies->drive = $data;
                $mirrorcopies->provider = $mirror;
                $mirrorcopies->apikey = $keys['keys'];
                $mirrorcopies->save();
                return true;
            }
        }
    }
}
