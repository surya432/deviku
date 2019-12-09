<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Laporan;
use DB;

use App\Setting;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    //
    public function index()
    {
        return view('user.laporan');
    }
    public function getlaporan()
    {
        $data = DB::table('laporans')->join('users', 'laporans.username', '=', 'users.id')->select('laporans.*', 'users.first_name')->orderBy('laporans.created_at', 'desc')->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return '<button type="button" id="btnShow" data-id="' . $data->id . '" data-date="' . $data->created_at . '" data-userid="' . $data->username . '" data-comment="' . $data->laporan . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>';
            })
            ->order(function ($data) {
                if (request()->has('created_at')) {
                    $data->orderBy('created_at', 'desc');
                }
            })
            ->make(true);
    }
    public function addlaporan(Request $request)
    {
        if (!empty($request->input("id"))) {
            $dataType = Laporan::find($request->input("id"));
            $dataType->username = $request->input("user_id");
            $dataType->laporan = $request->input("comment");
            $dataType->save();
            $dataTypeasd = "Update Success";
            return response()->json($dataTypeasd, 201);
        }
        $dataType = new Laporan;
        $dataType->username = $request->input("user_id");
        $dataType->laporan = $request->input("comment");
        $dataType->save();
        $dataTypeasd = "Simpan Success";
        return response()->json($dataTypeasd, 201);
    }
}
