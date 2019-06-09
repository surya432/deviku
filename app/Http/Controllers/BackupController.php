<?php

namespace App\Http\Controllers;
use DB;
use App\Setting;
use App\BackupFilesDrive;
use App\Content;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use HelperController;
    public function index()
    {
        //
        $settingData = Setting::find(1);
        $this->AutoDeleteGd();
        $dataContent =  DB::table('contents')
          ->whereNotIn('url', DB::table('backups')->whereNotNull('f720p')->pluck('url'))
          ->where('f720p', 'NOT LIKE', '%picasa%')
          ->whereNotNull('f720p')
          ->take(5)
          ->get();
        $dataresult = array();
        foreach ($dataContent as $dataContents) {
          $f20p = $this->CheckHeaderCode($dataContents->f720p);
          if ($f20p) {
            $content = array('url' => $dataContents->url, 'title' => $dataContents->title);
            $datass = BackupFilesDrive::firstOrCreate($content);
            $copyID = $this->copygd($this->GetIdDriveTrashed($dataContents->f720p), $settingData->folderbackup, $dataContents->url, $settingData->tokenDriveAdmin);
            if (isset($copyID['id'])) {
              //$datass = Content::where('title', $dataContents->title);
              $datass->f720p = $copyID['id'];
              $datass->save();
            }
            array_push($dataresult, $copyID);
          } else {
            $content = Content::find($dataContents->id);
            $content->f720p = null;
            $content->save();
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
