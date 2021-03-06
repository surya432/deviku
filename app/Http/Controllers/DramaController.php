<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Drama;
use App\Country;
use App\Type;
use Cache;
use DB;
use App\Content;
use Yajra\DataTables\Facades\DataTables;

class DramaController extends Controller
{
    use HelperController;
    public function index()
    {
        $country = Country::all();
        $Type = Type::all();
        $status = Drama::groupBy('status')->select('status')->get();
        return view("dashboard.drama")->with("country", $country)->with("status", $status)->with("Type", $Type);
    }
    public function get()
    {
        // Cache::forget('Drama');
        /* if (Cache::has('Drama')) {
            $data = Cache::get('Drama');
        }else{
            $data = Drama::with('country')->with('type')->orderBy('id','desc')->get();
            Cache::forever('Drama', $data);
        } */
        $data = Drama::with('country')->with('type')->orderBy('id', 'desc')->get();

        return Datatables::of($data)
        ->addIndexColumn()


            ->addColumn('type', function ($data) {
                return $data->type->name;
            })

            ->addColumn('action', function ($data) {
                $extBtn = "";
                if ($data->folderid == "" || is_null($data->folderid)) {
                    $extBtn = '<a href="' . route("createFolderDrive") . '?id=' . $data->id . '" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> Create Folder</a>';
                }
                return '<div class="btn-group" role="group" aria-label="Command Action">' . $extBtn . '
                <a href="' . route("eps", $data->id) . '" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> show</a>
                <button type="button" id="btnShow" data-id="' . $data->id . '" data-status="' . $data->status . '" data-folderid="' . $data->folderid . '" data-type_id="' . $data->type_id . '" data-country_id="' . $data->country_id . '" data-title="' . $data->title . '"  data-torrentlink="' . $data->torrentlink . '"  data-torrentlink="' . $data->subsceneslink . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></button>
                <button type="button" id="btnDelete" data-id="' . $data->id . '" data-status="' . $data->status . '" data-folderid="' . $data->folderid . '" data-type_id="' . $data->type_id . '" data-country_id="' . $data->country_id . '" data-title="' . $data->title . '"  data-torrentlink="' . $data->torrentlink . '"  data-torrentlink="' . $data->torrentlink . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></button></div>';
            })
            ->order(function ($query) {
                if (request()->has('id')) {
                    $query->orderBy('id', 'desc');
                }
            })
            ->make(true);
    }
    public function Post(Request $request)
    {
        Cache::forget('Drama');
        if (!empty($request->input("id"))) {
            $dataType = Drama::find($request->input("id"));
            $dataType->title = $request->input("title");
            $dataType->slug = $this->seoUrl($request->input("title"));
            $dataType->folderid = $request->input("folderid");
            $dataType->status = $request->input("status");
            $dataType->country_id = $request->input("country_id");
            $dataType->type_id = $request->input("type_id");
            $dataType->subsceneslink = $request->input("subsceneslink");
            $dataType->torrentlink = $request->input("torrentlink");
            $dataType->save();
            $dataTypeasd = "Update Success";
            return response()->json($dataTypeasd, 201);
        }
        $dataType = new Drama;
        $dataType->title = $request->input("title");
        $dataType->slug = $this->seoUrl($request->input("title"));
        $dataType->folderid = $request->input("folderid");
        $dataType->status = $request->input("status");
        $dataType->country_id = $request->input("country_id");
        $dataType->type_id = $request->input("type_id");
        $dataType->subsceneslink = $request->input("subsceneslink");
        $dataType->torrentlink = $request->input("torrentlink");
        $dataType->save();
        // $folderName =  $dataType->title . " [$dataType->id]";
        // $resultCurl = $this->GDCreateFolder($folderName);
        // $dataType = Drama::find($dataType->id);
        // if ($dataType) {
        //     $dataType->folderid = $resultCurl['id'];
        //     $dataType->save();
        // }
        $dataTypeasd = "Insert Success";
        return response()->json($dataTypeasd, 201);
    }
    public function Delete(Request $request)
    {
        $dataContent = Drama::find($request->input("id"));
        if (!is_null($dataContent)) {
            DB::table('contents')->where('drama_id', '=', $request->input("id"))->delete();
            DB::table('dramas')->where('id', '=', $request->input("id"))->delete();
            $dataContent = "Delete Success";
            cache::forget("Drama");
            return response()->json($dataContent, 201);
        }
        return response()->json("error Delete", 201);
    }
}
