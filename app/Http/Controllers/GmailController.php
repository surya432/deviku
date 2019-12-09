<?php

namespace App\Http\Controllers;

use App\Gmail;
use App\Setting;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;

class GmailController extends Controller
{
    //
    use HelperController;

    public function Index()
    {
        return view("dashboard.gmail");
    }
    public function Data()
    {
        $data = DB::table('gmails')
            ->select(
                'gmails.*',
                DB::raw("(SELECT Count(*) FROM mirrors where mirrors.token = gmails.token) as totalfiles")
            )
            ->get();
        return Datatables::of($data)
        ->addIndexColumn()
            ->addColumn('apiUrl', function ($data) {
                return (!empty($data->apiUrl)) ? "true" : "false";
            })
            ->addColumn('statusFolder', function ($data) {
                $folderCode = $this->CheckHeaderFolderCode($data->folderid);
                return ($folderCode) ? "true" : "false";
            })
            ->addColumn('action', function ($data) {
                return '
                <a href="https://drive.google.com/drive/folders/' . $data->folderid . '"  class="btn btn-xs btn-primary" target="_blank">Folder</a>
                <a href="/admin/gmail/token?id=' . $data->id . '"  class="btn btn-xs btn-primary" target="_blank">Check Token</a>
                <button type="button" id="btnShow" data-apiUrl="' . $data->apiUrl . '" data-tipe="' . $data->tipe . '" data-id="' . $data->id . '" data-email="' . $data->email . '" data-token="' . $data->token . '" data-folderid="' . $data->folderid . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></button>
                <button type="button" id="btnDelete" data-id="' . $data->id . '" data-email="' . $data->email . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></button>';
            })
            ->make(true);
    }
    public function Post(Request $request)
    {
        // if ($request->input('tipe') !== null) {
        //    $tipe = "copy";
        // } else {
        //     $tipe = $request->input("tipe");
        // }
        // $gmail = \App\gmail::updateOrCreate(
        //     ['email' => strtolower($request->input("email")), 'folderid' => $request->input("folderid")],
        //     ['tipe' => $tipe, 'apiUrl' => Input::get("apiUrl"),'token'=>$request->input("token")]
        // );
        if (!empty($request->input("id"))) {
            $gmail = Gmail::find($request->input("id"));
            if (!empty($request->input("email"))) {
                $gmail->email = strtolower(Input::get("email"));
            }
            if (!empty($request->input("token"))) {
                $gmail->token = Input::get("token");
            }
            if (!empty($request->input("apiUrl"))) {
                $gmail->apiUrl = Input::get("apiUrl");
            }
            if (!empty($request->input("folderid"))) {
                $gmail->folderid = Input::get("folderid");
            }
            if (!empty($request->input("tipe"))) {
                $gmail->tipe = Input::get("tipe");
            }
            $gmail->save();
            return response()->json($gmail, 200);
        }
        $gmail = new Gmail;
        $gmail->email = strtolower(Input::get("email"));
        $gmail->token = Input::get("token");
        $gmail->apiUrl = Input::get("apiUrl");
        $gmail->folderid = Input::get("folderid");
        if (!empty($request->input("tipe"))) {
            $gmail->tipe = $request->input("tipe");
        } else {
            $gmail->tipe = "copy";
        }
        $gmail->save();
        return response()->json($gmail, 200);
    }

    public function Delete(Request $request)
    {
        $dataContent = Gmail::where('id', $request->input("id"))->first();
        $content = \App\BackupFilesDrive::where("tokenfcm", $dataContent->token)->get();
        if ($content) {
            foreach ($content as $content) {
                $idcopy = $content->f720p;
                $tokens = $dataContent->token;
                if (!is_null($idcopy) && !is_null($tokens)) {
                    $this->deletegd($this->GetIdDrive($idcopy), $tokens);
                    $content->delete();
                } else {
                    $content->delete();
                }
            }
        }
        $dataContent22 = \App\Trash::where('token', $dataContent->token)->first();
        if (is_null($dataContent22)) {
            $dataContent->delete();
            $dataContent22 = "Delete Success";
            return response()->json($dataContent22, 200);
        }
        return response()->json("Error Delete", 404);
    }
    public function getToken(Request $request)
    {
        $email = Gmail::where("id", $request->input("id"))->first();
        return dd($this->get_token($email->token));
    }
    public function getTokenAdmin()
    {
        $data = Setting::find(1);

        return dd($this->get_token($data->tokenDriveAdmin));
    }
    public function addEmail(Request $request)
    {
        $gmail = new Gmail;
        $gmail->email = strtolower($request->input('email'));
        $gmail->token = $request->input("token");
        $gmail->apiUrl = $request->input("apiUrl");
        $gmail->folderid = $request->input("folderid");
        if ($request->input('tipe') !== null) {
            $gmail->tipe = "copy";
        } else {
            $gmail->tipe = $request->input("tipe");
        }
        $gmail->save();

        return response()->json($gmail, 200);
    }
}
