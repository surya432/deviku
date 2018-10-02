<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gmail;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
class GmailController extends Controller
{
    //
    public function Index(){
        return view("dashboard.gmail");
    }
    public function Data(){
        $data = Gmail::all();
        return Datatables::of($data)
        ->addColumn('action', function ($data) {
             return '<button type="button" id="btnShow" data-id="'.$data->id.'" data-email="'.$data->email.'" data-token="'.$data->token.'" data-folderid="'.$data->folderid.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
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
        $gmail= Gmail::find($request->input("id"));
        $gmail->delete();
        return response()->json($gmail,201);
    }
}
