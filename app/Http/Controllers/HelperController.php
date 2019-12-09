<?php
namespace App\Http\Controllers;

use App\BackupFilesDrive;
use App\Content;
use App\Gmail;
use App\Mirror;
use App\Setting;
use App\Trash;
use DB;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;

trait HelperController
{
    public function seoUrl($string)
    {
        $string = trim($string); // Trim String
        $string = strtolower($string); //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string); //Strip any unwanted characters
        $string = preg_replace("/[\s-]+/", " ", $string); // Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s_]/", "-", $string); //Convert whitespaces and underscore to dash
        return $string;
    }
    public function getHeaderCode($url)
    {
        $url = 'https://drive.google.com/file/d/' . $url . '/view';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true); // we want headers
        curl_setopt($ch, CURLOPT_NOBODY, true); // we don't need body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpcode;
    }
    public function getHeaderFolderCode($id)
    {
        $curl = $this->viewsource("https://www.googleapis.com/drive/v2/files/" . $id . "?supportsAllDrives=true&supportsTeamDrives=true");
        $data = json_decode($curl, true);
        if (isset($data["shared"])) {
            return $data["shared"];
        } else {
            return false;
        }
    }
    public function viewsource($url)
    {
        $tokens = gmail::where('tipe', 'copy')->select("token")->inRandomOrder()->first();
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $head[] = "Connection: keep-alive";
        $head[] = "Keep-Alive: 300";
        $head[] = 'Authorization: ' . $this->get_token($tokens->token);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //     "Authorization: ".$this->get_token($tokens)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_REFERER, 'http://dldramaid.xyz/');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }
    //
    public function singkronfile($id_folder, $tokenPage = null)
    {
        $curl = $this->viewsource("https://www.googleapis.com/drive/v3/files?q='" . $id_folder . "'+in+parents&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&&pageSize=250&orderby=modifiedByMeTime&supportsAllDrives=true&supportsTeamDrives=true");
        return json_decode($curl, true);
    }
    public function singkronToWeb($id_folder, $tokenPage = null)
    {
        $curl = $this->viewsource("https://www.googleapis.com/drive/v3/files?q='" . $id_folder . "'+in+parents&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&&pageSize=250&orderby=modifiedByMeTime&supportsAllDrives=true&supportsTeamDrives=true");
        return json_decode($curl, true);
    }
    public function getEmbed($data)
    {
        $embed = '';
        foreach ($data as $eps) {
            $embed .=
            '<h3>' . $eps->title . '</h3>
            <p><iframe src="' . route("viewEps", $eps->url) . '" width="100%" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>';
        }
        return $embed;
    }
    public function GetTags($data)
    {
        $embed = '';
        $eps = $data->eps;
        $title = $data->title;
        $category = $data->country->name;
        $status = $data->status;
        $jenis = $data->type->name;
        $tag = "Nonton " . $title . " Subtile Indonesia, Nonton " . $title . " Sub Indonesia, Nonton " . $jenis . " " . $category . " " . $title . " Sub Indo, Nonton " . $jenis . " " . $category . " " . $title . " Subtitle Indo online, Nonton " . $title . " Sub Indonesia Online, Nonton " . $jenis . " " . $category . " Sub Indo, Nonton " . $jenis . " " . $category . " Subtitle Indo, Nonton " . $jenis . " " . $title . " Subtitle Indonesia, Download " . $jenis . " " . $category . " " . $title . " Sub Indo, Download " . $title . " Sub Indonesia, Download " . $title . " " . $category . " Subtitle Indonesia, Download " . $jenis . " " . $title . " Subtitle Indonesia, Download " . $category . " " . $title . " Subtitle Indonesia, Download " . $jenis . " " . $category . " Sub Indo, Download " . $jenis . " " . $category . " Subtitle Indo, nonton online drama korea sub indo,drakor id,nonton drama korea,nonton drama korea online,nonton streaming drama korea,nonton drama online,nontondrama tv,nonton online drama korea,nonton movie korea,nonton korea online,nonton streaming korea,nonton drama korea streaming,nonton movie drama korea,k drama online,nonton korea streaming,nonton streaming drama korea terbaru,nonton korea drama online,nonton online drama,nonton on line,nonton drama,nonton film online korea terbaru,streaming k drama,nonton film drama korea online,k drama streaming,nonton web drama korea,nonton film online drama korea sub indo,nonton drama korea indo sub,nonton movie,nonton film,nonton streaming,drama korea terbaru,nonton drakor,kdrama,nonton film korea,k drama sub indo,nonton streaming online, drakorindo,drakor,drama korea terbaru,nonton drakor,nonton online drama korea sub indo,drakor id,nonton drama korea,nonton drama korea online,nonton streaming drama korea,nonton drama online,nontondrama tv,nonton online drama korea,nonton movie korea,nonton korea online,nonton streaming korea,nonton drama korea streaming,nonton movie drama korea,k drama online,nonton korea streaming,nonton streaming drama korea terbaru,nonton korea drama online,nonton online drama,nonton on line,nonton drama,nonton film online korea terbaru,streaming k drama,nonton film drama korea online,k drama streaming,nonton web drama korea,nonton film online drama korea sub indo,nonton drama korea indo sub,nonton movie,nonton film,nonton streaming,drama korea terbaru,nonton drakor,kdrama,nonton film korea,k drama sub indo,nonton streaming online, drakorindo,drakor,drama korea terbaru,nonton drakor";
        foreach ($eps as $eps) {
            $embed .=
            '<h3>' . $eps->title . '</h3><p><iframe src="' . route("viewEps", $eps->url) . '" width="100%" height="400" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p>';
        }
        $result = array("title" => $title, "tag" => $tag, "country" => $category, "category" => $jenis, "status" => $status, "iframe" => base64_encode($embed));
        return $result;
    }
    public function postWeb($site, $drama_id, $header, $body)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $site . "/wp-json/wp/v2/posts/" . $drama_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $header,
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
    public function postNewWeb($site, $header, $body)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $site . "/wp-json/wp/v2/posts/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $header,
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
    private function post_url($url, $body)
    {
        $ch = @curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($body != null) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $headers = [];
        $headers[] = 'cache-control: no-cache';
        $headers[] = 'application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }
    private function link_upload($link, $name = null)
    {
        $links = null;
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $link, $id)) {
            //$links = "http://cdn.dldramaid.xyz:5000/videos/apis/".$id[1]."/".$name;
            $links = urlencode("https://www.googleapis.com/drive/v3/files/" . $id[1] . "?alt=media&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&supportsAllDrives=true&supportsTeamDrives=true");
        }
        return $links;
    }

    public function refresh_token($token, $apiUrl)
    {
        $tokenencode = urlencode($token);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.googleapis.com/oauth2/v4/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "$apiUrl&refresh_token=$tokenencode&grant_type=refresh_token",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return null;
        } else {
            return $response;
        }
    }
    public function get_token($tokens)
    {
        if (!Cache::has($tokens)) {
            $gmail = Gmail::where('token', $tokens)->whereNotNull('apiUrl')->first();
            $apiUrl = false;
            if ($gmail) {
                $apiUrl = $gmail->apiUrl;
                $result_curl23 = $this->refresh_token($tokens, $apiUrl);
                if ($result_curl23) {
                    $checklinkerror = json_decode($result_curl23, true);
                    if (isset($checklinkerror['access_token'])) {
                        $gmail = Gmail::where('token', $tokens)->first();
                        if (!is_null($gmail)) {
                            $dataGmail = Gmail::where('id', $gmail->id)->first();
                            $dataGmail->touch();
                        }
                        $get_info23 = "Bearer " . $checklinkerror['access_token'];
                        Cache::put($tokens, $get_info23, now()->addMinutes(55));
                        return $get_info23;
                    } else {
                        return "Bearer Error";
                    }
                }
            }

        } else {
            return Cache::get($tokens);
        }
    }

    public function CheckHeaderCode($idDrive)
    {
        if (!Cache::has('CHECKHEADER-' . md5($idDrive))) {
            $expiresCacheAt = Setting::find(1)->expiresCacheAt;
            $statusCode = $this->getHeaderFolderCode($idDrive);
            Cache::put('CHECKHEADER-' . md5($idDrive), $statusCode, $expiresCacheAt);
            return $statusCode;
        }
        $statusCode = Cache::get('CHECKHEADER-' . md5($idDrive));
        return $statusCode;
    }
    public function CheckHeaderFolderCode($idDrive)
    {
        if (!Cache::has('FolderCode' . md5($idDrive))) {
            $expiresCacheAt = Setting::find(1)->expiresCacheAt;
            $statusCode = $this->getHeaderFolderCode($idDrive);
            Cache::put('FolderCode' . md5($idDrive), $statusCode, $expiresCacheAt);
            return $statusCode;
        }
        $statusCode = Cache::get('FolderCode' . md5($idDrive));
        return $statusCode;
    }
    public function GetIdDriveForPlayer($urlVideoDrive)
    {
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $urlVideoDrive, $id)) {
            return $id[1];
        } else {
            return $urlVideoDrive;
        }
    }
    public function GetIdDrive($urlVideoDrive)
    {
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $urlVideoDrive, $id)) {
            return $id[1];
        } else {
            return $urlVideoDrive;
        }
    }
    public function addToTrashes($idcopy, $token)
    {
        try {
            $trashes = new \App\Trash();
            $trashes->idcopy = $idcopy;
            $trashes->token = $token;
            $trashes->save();
        } catch (Exception $e) {
            echo $e->errorMessage();
        }
    }
    public function copygd($driveId, $folderid, $title, $token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/$driveId/copy?supportsAllDrives=true&supportsTeamDrives=true",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"name\":\"$title\",\"parents\":[\"$folderid\"]}",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $this->get_token($token),
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
            if ($response) {
                $response = json_decode($response, true);
                return $response;
            }
        }
    }
    public function changePermission($id, $token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $id . '/permissions?supportsAllDrives=true&supportsTeamDrives=true');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"role\": \"reader\",\"type\": \"anyone\"}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = array();
        $headers[] = 'Authorization: ' . $this->get_token($token);
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($result, true);
        return true;
    }
    public function deletegd($id, $token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/$id?supportsAllDrives=true&supportsTeamDrives=true",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $this->get_token($token),
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
    public function emptytrash($token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/trash",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $this->get_token($token),
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
    public function GDCopy($urlVideo, $nameVideo, $kualitas)
    {
        //$gmails =  DB::table('gmails')->inRandomOrder()->first();
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $urlVideo, $id)) {
            $sizeCount = Setting::find(1)->sizeCount;
            $gmails = Gmail::whereNotIn('token', function ($query) use ($sizeCount) {
                $query->select('token')
                    ->from('mirrors')->groupBy('token')->havingRaw('COUNT(*) >= ' . $sizeCount);
            })->where('tipe', "copy")->inRandomOrder()->first();
            $title = $nameVideo . '-' . $kualitas . '.mp4';
            if (is_null($gmails)) {
                return null;
            } else {
                try {
                    $copyid = $this->copygd($id['1'], $gmails->folderid, $title, $gmails->token);
                    if (isset($copyid['id'])) {
                        $mirror = new Mirror();
                        $mirror->idcopy = $copyid['id'];
                        $mirror->kualitas = $kualitas;
                        $mirror->token = $gmails->token;
                        $mirror->url = $urlVideo;
                        $mirror->save();
                        return $copyid['id'];
                    } else {
                        return null;
                    }
                } catch (Exception $e) {
                    return $e->errorMessage();
                }
            }
        }
    }
    public function GDMoveFolder($id, $uploadfolder)
    {
        $settingData = Setting::find(1);
        $oldFolder = $settingData->folderUpload;
        $tokenAdmin = $settingData->tokenDriveAdmin;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $id . '?supportsAllDrives=true&supportsTeamDrives=true&addParents=' . $uploadfolder . '&removeParents=' . $oldFolder);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{}");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = array();
        $headers[] = 'Authorization: ' . $this->get_token($tokenAdmin);
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
    }
    public function GDCreateFolder($title)
    {
        $gmail = Gmail::where('tipe', "master")->whereNotNull('apiUrl')->first();
        $folderid = $gmail->foderid;
        $tokenAdmin = $gmail->token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"name\": \"$title\",\"parents\": [\"$folderid\"],\"mimeType\": \"application/vnd.google-apps.folder\"}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = array();
        $headers[] = 'Authorization: ' . $this->get_token($tokenAdmin);
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }
    public function GetIdDriveTrashed($urlVideoDrive)
    {
        if (preg_match('@https?://(?:[\w\-]+\.)*(?:drive|docs)\.google\.com/(?:(?:folderview|open|uc)\?(?:[\w\-\%]+=[\w\-\%]*&)*id=|(?:folder|file|document|presentation)/d/|spreadsheet/ccc\?(?:[\w\-\%]+=[\w\-\%]*&)*key=)([\w\-]{28,})@i', $urlVideoDrive, $id)) {
            return $id[1];
        } else {
            return $urlVideoDrive;
        }
    }
    public function AutoDeleteGd()
    {
        $datass = Trash::take(20)->get();
        if ($datass) {
            foreach ($datass as $datass) {
                $idcopy = $datass->idcopy;
                $tokens = $datass->token;
                if (!is_null($idcopy) && !is_null($tokens)) {
                    if ($this->deletegd($this->GetIdDriveTrashed($idcopy), $tokens)) {
                        $datass->delete();
                    }
                } else {
                    $datass->delete();
                }
            }
        }
        return true;
    }
    public function AutoBackupDrive()
    {
        $seconds = 1000 * 60 * 15;
        $value = Cache::remember('backupgd', $seconds, function () {
            $settingData = Gmail::where('tipe', "backup")->inRandomOrder()->first();
            $this->AutoDeleteGd();
            $dataContent = DB::table('contents')
                ->whereNotIn('url', DB::table('backups')->whereNotNull('f720p')->pluck('url'))
                ->where('f720p', 'NOT LIKE', '%picasa%')
                ->whereNotNull('f720p')
                ->whereNotIn('url', DB::table('backups')->whereNotNull('f360p')->pluck('url'))
                ->where('f360p', 'NOT LIKE', '%picasa%')
                ->whereNotNull('f360p')
                ->inRandomOrder()
                ->take(5)
                ->get();
            foreach ($dataContent as $dataContent) {
                $f20p = $this->CheckHeaderCode($dataContent->f720p);
                if ($f20p) {
                    $content = array('url' => $dataContent->url, 'title' => $dataContent->title . "-720p");
                    $datass = BackupFilesDrive::firstOrCreate($content);
                    $copyID = $this->copygd($this->GetIdDriveTrashed($dataContent->f720p), $settingData->folderid, $dataContent->url, $settingData->token);
                    if (isset($copyID['id'])) {
                        //$datass = Content::where('title', $dataContents->title);
                        $this->changePermission($copyID['id'], $settingData->token);
                        $datass->f720p = $copyID['id'];
                        $datass->save();
                    }
                } else {
                    $content = Content::find($dataContent->id);
                    $content->f720p = null;
                    $content->save();
                }
                $f360p = $this->CheckHeaderCode($dataContent->f360p);
                if ($f360p) {
                    $content = array('url' => $dataContent->url, 'title' => $dataContent->title . "-360p");
                    $datass = BackupFilesDrive::firstOrCreate($content);
                    $copyID = $this->copygd($this->GetIdDriveTrashed($dataContent->f360p), $settingData->folderid, $dataContent->url, $settingData->token);
                    if (isset($copyID['id'])) {
                        //$datass = Content::where('title', $dataContents->title);
                        $this->changePermission($copyID['id'], $settingData->token);
                        $datass->f720p = $copyID['id'];
                        $datass->save();
                    }
                } else {
                    $content = Content::find($dataContent->id);
                    $content->f720p = null;
                    $content->save();
                }
            }
        });
        return true;
    }
    public function getDetailDrama($url)
    {

        $goutteClient = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
            'verify' => false,

        ));
        $goutteClient->setClient($guzzleClient);
        // $client = new Client();
        // $guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
        $crawler = $goutteClient->request('GET', $url);
        // $client->setClient($guzzleClient);
        // $crawler = $client->request('GET', $url);
        $keys = [];
        //  $crawler->filter('.left >p:nth-of-type(1)')->each(function ($node){
        $getkeys = $crawler->filter('.left >p')->each(function ($node) {
            return array(strtolower($node->filter('strong')->text()) => $node->filter('span')->text());
        });
        foreach ($getkeys as $a => $b) {
            foreach ($b as $c => $d) {
                $ca = str_replace(":", "", $c);
                $keys[$ca] = $d;
            }
        }
        $getPlot = $crawler->filter('.right >.info')->each(function ($node) {
            return ["plot" => $node->filter('p')->text()];
        });
        foreach ($getPlot as $a => $b) {
            foreach ($b as $c => $d) {
                $keys[$c] = $d;
            }
        }
        return $keys;
        //>span:nth-of-type(1)
        // return  $crawler->filter('.left')->text();
    }
    public function show_Spanish($n, $m)
    {
        return "The number {$n} is called {$m} in Spanish";
    }
}
