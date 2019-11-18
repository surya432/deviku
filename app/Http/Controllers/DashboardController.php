<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use App\Drama;
use App\Country;
use App\Type;
use Cache;
use DB;
use App\Content;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    //
    public function index()
    {

        return view('dashboard.index');
    }
    public function get()
    {

        $data = Drama::with('country')->with('type')->orderBy('updated_at', 'desc')->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                return '<div class="btn-group" role="group" aria-label="Command Action">
                    <a href="' . route("eps", $data->id) . '" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> show</a>
                    <button type="button" id="btnSingkronWeb" data-title="' . $data->title . '" data-drama_id="' . $data->id . '" class="btn btn-primary btn-xs">
                        <i class="fa fa-refresh fa-fw"></i>  Wordpress
                    </button>
                    </div>';
            })
            ->order(function ($query) {
                if (request()->has('id')) {
                    $query->orderBy('id', 'desc');
                }
            })
            ->make(true);
    }
}
