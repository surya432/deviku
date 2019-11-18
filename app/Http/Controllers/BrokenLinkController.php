<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Country;
use App\Drama;
use App\Content;
use App\Type;
use App\Brokenlink;
use Yajra\DataTables\Facades\DataTables;

class BrokenLinkController extends Controller
{
    //
    use HelperController;

    function index()
    {
        return view('brokenlinks.index');
    }
    function brokenlinksIndexTables()
    {

        $data = DB::table('dramas')->whereIn('id', function ($query) {
            $query->from('contents')->select('drama_id')->whereIn('id', function ($query) {
                $query->from('brokenlinks')->select('contents_id')->groupBy('contents_id')->get();
            })->select('drama_id')->groupBy('drama_id')->get();
        })->orderBy('id', 'desc')->get();
        return Datatables::of($data)
            ->addColumn('folderids', function ($data) {
                if ($data->folderid !== "") {
                    return 'true';
                } else {
                    return 'false';
                }
            })
            ->addColumn('action', function ($data) {
                $extBtn = "";
                if ($data->folderid == "") {
                    $extBtn = '<button type="button" id="btnaddFolder" data-id="' . $data->id . '" data-status="' . $data->status . '" data-folderid="' . $data->folderid . '" data-title="' . $data->title . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus"></i> Create Folder</button>';
                }
                return '<div class="btn-group" role="group" aria-label="Command Action">
            <a href="' . route("DetailBrokenLink", $data->id) . '" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> show</a>
            <button type="button" id="btnDelete" data-id="' . $data->id . '" data-status="' . $data->status . '" data-folderid="' . $data->folderid . '" data-title="' . $data->title . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button></div>';
            })
            ->order(function ($query) {
                if (request()->has('id')) {
                    $query->orderBy('id', 'desc');
                }
            })
            ->make(true);
    }
    function SetEpsFixed(Request $request)
    {
        $data = Brokenlink::where('contents_id', $request->input('id'))->delete();
        $dataTypeasd = "sukses di Jalankan";
        return response()->json($dataTypeasd, 201);
    }
    function DetailBrokenLinks($id)
    {
        $data = DB::table('contents')->whereIn('id', function ($query) {
            $query->from('brokenlinks')->select('contents_id')->get();
        })->where('drama_id', $id)->orderBy('title', 'asc')->get();
        return Datatables::of($data)
        ->addIndexColumn()


            ->addColumn('f360ps', function ($data) {

                $f360p = $this->CheckHeaderCode($data->f360p);
                return ($f360p) ? "Ok" : "false";
            })
            ->addColumn('f720ps', function ($data) {
                $f720p = $this->CheckHeaderCode($data->f720p);
                return ($f720p) ? "Ok" : "false";
            })
            ->addColumn('action', function ($data) {
                $urlhost = request()->getHost();

                $f360p = '<a href="https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($data->f720p) . '&videoName=' . $data->url . '-360p" target="_blank" name="url_f360p" id="url_f360p" data-clipboard-text="https://www.googleapis.com/drive/v3/files/' . $this->GetIdDrive($data->f360p) . '?alt=media&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&name=' . $data->url . '-360p.mp4" value="https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($data->f720p) . '&videoName=' . $data->url . '-360p" class="btn btn-xs btn-primary btncopy360p">Copy 360p</a>';
                $f720p = '<a href="https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($data->f360p) . '&videoName=' . $data->url . '-360p" target="_blank" type="button" name="url_f720p" id="url_f720p" data-clipboard-text="https://www.googleapis.com/drive/v3/files/' . $this->GetIdDrive($data->f720p) . '?alt=media&key=AIzaSyARh3GYAD7zg3BFkGzuoqypfrjtt3bJH7M&name=' . $data->url . '-720p.mp4"  value="https://' . $urlhost . '/proxyDrive?id=' . $this->GetIdDrive($data->f360p) . '&videoName=' . $data->url . '-720p" class="btn btn-xs btn-primary btncopy720p">Copy 720p</a>';

                return '<div class="btn-group" role="group" aria-label="Command Action">
                ' . $f360p . $f720p . '
                <a href="' . route("viewEps", $data->url) . '" target="_blank" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> show</a>
                <button type="button" id="btnShow" data-id="' . $data->id . '" data-drama_id="' . $data->drama_id . '" data-status="' . $data->status . '" data-title="' . $data->title . '" data-f720p="' . $data->f720p . '" data-f360p="' . $data->f360p . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
                <button type="button" id="btnFixed" data-id="' . $data->id . '" data-drama_id="' . $data->drama_id . '" data-status="' . $data->status . '" data-title="' . $data->title . '" data-f720p="' . $data->f720p . '" data-f360p="' . $data->f360p . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Set Fixed</button>
                <button type="button" id="btnDelete" data-id="' . $data->id . '" data-drama_id="' . $data->drama_id . '" data-status="' . $data->status . '" data-title="' . $data->title . '" data-f720p="' . $data->f720p . '" data-f360p="' . $data->f360p . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button></div>';
            })
            ->order(function ($data) {
                if (request()->has('id')) {
                    $data->orderBy('id', 'desc');
                }
            })
            ->make(true);
    }
    function DetailBrokenLink($id)
    {
        $checkPost = Drama::find($id);
        if (is_null($checkPost)) {
            return abort('404');
        }

        $value = Drama::where('id', $id)->with('country')->with('type')->with('eps')->orderBy('id', 'desc')->first();
        return view('brokenlinks.detail')->with('result', $value);
    }
}
