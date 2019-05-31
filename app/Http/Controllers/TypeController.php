<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Type;
use Cache;
use Yajra\DataTables\Facades\DataTables;

class TypeController extends Controller
{
    //
    use HelperController;

    public function index()
    {
        return view('dashboard.type');
    }
    public function Get()
    {
        cache::forget("Type");
        if (Cache::has('Type')) {
            $data = Cache::get('Type');
        } else {
            $data = Type::all();
            $data = Cache::get('Type', $data);
        }
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<div class="btn-group" role="group" aria-label="Basic example">
                    <a href="http://asdasdsad/' . $data->id . '" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> show</a>
                    <button type="button" id="btnShow" data-id="' . $data->id . '" data-name="' . $data->name . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
                    <button type="button" id="btnDelete" data-id="' . $data->id . '" data-name="' . $data->name . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button></div>';
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
        cache::forget("Type");
        if (!empty($request->input("id"))) {
            $dataType = Type::find($request->input("id"));
            $dataType->name = $request->input("name");
            $dataType->slug = $this->seoUrl($request->input("name"));;
            $dataType->save();
            return response()->json($dataType, 201);
        }
        $dataType = new Type;
        $dataType->name = $request->input("name");
        $dataType->slug = $this->seoUrl($request->input("name"));;
        $dataType->save();
        return response()->json($dataType, 201);
    }
    public function Delete(Request $request)
    {
        $dataType = Type::find($request->input("id"));
        $dataType->delete();
        cache::forget("Type");
        return response()->json($dataType, 201);
    }
}
