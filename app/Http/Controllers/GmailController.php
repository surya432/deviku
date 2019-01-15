<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gmail;
use App\HelperController;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
class GmailController extends Controller
{
    //
    use HelperController;

    public function Index(){
        return view("dashboard.gmail");
    }
    public function Data(){
        $data = Gmail::all();
        return Datatables::of($data)
		 ->addColumn('action', function ($data) {
             return '
			 <a href="https://drive.google.com/drive/folders/'.$data->folderid.'"  class="btn btn-xs btn-primary" target="_blank">Folder</a>
			 <button type="button" id="btnShow" data-id="'.$data->id.'" data-email="'.$data->email.'" data-token="'.$data->token.'" data-folderid="'.$data->folderid.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
             <button type="button" id="btnDelete" data-id="'.$data->id.'" data-email="'.$data->email.'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button>';
         })
        ->make(true);
    }
    public function Post(Request $request){
        if(!empty($request->input("id"))){
            $gmail = Gmail::find($request->input("id"));
            $gmail->email = Input::get("email");
            $gmail->token = Input::get("token");
            $gmail->folderid = Input::get("folderid");
            $gmail->save();
            return response()->json($gmail,201);

        }
        $gmail = new Gmail;
        $gmail->email = Input::get("email");
        $gmail->token = Input::get("token");
        $gmail->folderid = Input::get("folderid");
        $gmail->save();
        return response()->json($gmail,201);
    }

    public function Delete(Request $request){
        $dataContent= Gmail::find($request->input("id"));
		if(!is_null($dataContent)){
            $dataContent->delete();
            $dataContent = "Delete Success";
            return response()->json($dataContent,201);
        }
        return response()->json("error Delete",201);
    }
    public function getToken(Request $request){
        $email = Gmail::find($request->input("id"))->first();
        return $this->get_token($email->token);
    }
}
