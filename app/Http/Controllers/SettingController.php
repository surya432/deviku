<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;

class SettingController extends Controller
{
    //SettingController
    public function index()
    {
        return view('dashboard.setting');
    }
    public function get()
    {
        $data = Setting::all();
        return response()->json($data, 201);
    }
    public function post(Request $request)
    {
        $data = Setting::find(1);
        $data->site_name = $request->input("site_name");
        $data->folder720p = $request->input("folder720p");
        $data->folder360p = $request->input("folder360p");
        $data->folderUpload = $request->input("folderUpload");
        $data->tokenDriveAdmin = $request->input("tokenDriveAdmin");
        $data->folderbackup = $request->input("folderbackup");
        $data->apiUrl = $request->input("apiUrl");
        $data->tokenViu = $request->input("tokenViu");
        $data->path_hardsub = $request->input("path_hardsub");
        $data->viuSenin = $request->input("viuSenin");
        $data->viuSelasa = $request->input("viuSelasa");
        $data->viuRabu = $request->input("viuRabu");
        $data->viuKamis = $request->input("viuKamis");
        $data->viuJumat = $request->input("viuJumat");
        $data->viuSabtu = $request->input("viuSabtu");
        $data->viuMinggu = $request->input("viuMinggu");
        $data->sizeCount = $request->input("sizeCount");
        $data->expiresCacheAt = $request->input("expiresCacheAt");
        $data->dayFiles = $request->input("dayFiles");
        $data->save();
        return response()->json($data, 201);
    }
}
