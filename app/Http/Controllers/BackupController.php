<?php

namespace App\Http\Controllers;

use App\BackupFilesDrive;
use App\Content;
use App\gmail;
use DB;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use HelperController;
    public function deletegdFromDB()
    {
        // $this->AutoDeleteGd();
        $dataresult = array();

        $datass = \App\Trash::take(20)->get();
        if ($datass) {
            foreach ($datass as $datass) {
                $idcopy = $datass->idcopy;
                $tokens = $datass->token;
                if (!is_null($idcopy) && !is_null($tokens)) {
                    if ($this->deletegd($this->GetIdDriveTrashed($idcopy), $tokens)) {
                        $datass->delete();
                        array_push($dataresult, $datass->idcopy." Delete");
                    }else{
                        array_push($dataresult, $datass->idcopy." Delete Error");

                    }
                } else {
                    $datass->delete();
                }
            }
        }
        return response()->json($dataresult, 200);
    }
    public function index()
    {
        //
        $dataresult = array();
        $settingData = gmail::where('tipe', 'backup')->inRandomOrder()->first();
        if ($settingData) {
            //$this->AutoDeleteGd();
            DB::table('backups')->whereNull('f720p')->delete();
            $dataContent = DB::table('contents')
                ->whereNotIn('url', DB::table('backups')->pluck('url'))
                ->where('f720p', 'NOT LIKE', '%picasa%')
                ->whereNotNull('f720p')
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();
            foreach ($dataContent as $dataContents) {
                $f20p = $this->CheckHeaderCode($dataContents->f720p);
                if ($f20p) {
                    $content = array('url' => $dataContents->url, 'title' => $dataContents->url . "-720p");
                    $datass = BackupFilesDrive::firstOrCreate($content);
                    $copyID = $this->copygd($this->GetIdDriveTrashed($dataContents->f720p), $settingData->folderid, $dataContents->url . "-720p", $settingData->token);
                    if (isset($copyID['id'])) {
                        $this->changePermission($copyID['id'],$settingData->token);
                        $datass->f720p = $copyID['id'];
                        $datass->tokenfcm = $settingData->token;
                        $datass->save();
                        array_push($dataresult, $datass);
                    } else {
                        array_push($dataresult, $copyID);
                    }
                } else {
                    $content = Content::find($dataContents->id);
                    $content->f720p = null;
                    $content->save();
                }
            }
        }
        $settingData = gmail::where('tipe', 'backup')->inRandomOrder()->first();
        if ($settingData) {
            //$this->AutoDeleteGd();
            DB::table('backups')->whereNull('f720p')->delete();
            $dataContent = DB::table('contents')
                ->whereNotIn('url', DB::table('backups')->pluck('url'))
                ->where('f360p', 'NOT LIKE', '%picasa%')
                ->whereNotNull('f360p')
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();
            foreach ($dataContent as $dataContents) {
                $f20p = $this->CheckHeaderCode($dataContents->f360p);
                if ($f20p) {
                    $content = array('url' => $dataContents->url, 'title' => $dataContents->url . "-f360p");
                    $datass = BackupFilesDrive::firstOrCreate($content);
                    $copyID = $this->copygd($this->GetIdDriveTrashed($dataContents->f360p), $settingData->folderid, $dataContents->url . "-f360p", $settingData->token);
                    if (isset($copyID['id'])) {
                        $this->changePermission($copyID['id'],$settingData->token);
                        $datass->f720p = $copyID['id'];
                        $datass->tokenfcm = $settingData->token;
                        $datass->save();
                        array_push($dataresult, $datass);
                    } else {
                        array_push($dataresult, $copyID);
                    }
                } else {
                    $content = Content::find($dataContents->id);
                    $content->f720p = null;
                    $content->save();
                }
            }
        }
        return response()->json($dataresult);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\backup  $backup
     * @return \Illuminate\Http\Response
     */
    public function show(backup $backup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\backup  $backup
     * @return \Illuminate\Http\Response
     */
    public function edit(backup $backup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\backup  $backup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, backup $backup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\backup  $backup
     * @return \Illuminate\Http\Response
     */
    public function destroy(backup $backup)
    {
        //
    }
}
