<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
use Cache;

class CountryController extends Controller
{
    use HelperController;
    //
    public function Index()
    {
        return view("dashboard.country");
    }
    public function Data()
    {
        $cache = Cache::rememberForever('Country', function () {
            $data = Country::all();
            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    return '<button type="button" id="btnShow" data-id="' . $data->id . '" data-title="' . $data->name . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
                    <button type="button" id="btnDelete" data-id="' . $data->id . '" data-title="' . $data->name . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button>';
                })
                ->make(true);
        });
        return $cache;
    }
    public function Post(Request $request)
    {
        cache::forget("Country");
        if (!empty($request->input("id"))) {
            $dataCountry = Country::find($request->input("id"));
            $dataCountry->name = $request->input("name");
            $dataCountry->slug = $this->seoUrl($request->input("name"));;
            $dataCountry->save();
            return response()->json($dataCountry, 201);
        }
        $dataCountry = new Country;
        $dataCountry->name = $request->input("name");
        $dataCountry->slug = $this->seoUrl($request->input("name"));;
        $dataCountry->save();
        return response()->json($dataCountry, 201);
    }

    public function Delete(Request $request)
    {
        $dataCountry = Country::find($request->input("id"));
        $dataCountry->delete();
        cache::forget("Country");

        return response()->json($dataCountry, 201);
    }
}
