<?php

namespace App\Http\Controllers;

use App\Content;
use App\Country;
use App\Drama;
use App\Type;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DramaEpsController extends Controller
{
    //
    use HelperController;
    public function index($id)
    {

        //Cache::forget('Drama');
        $checkPost = Drama::find($id);
        if (is_null($checkPost)) {
            return abort('404');
        }
        $country = Country::all();
        $Type = Type::all();
        $status = Drama::groupBy('status')->select('status')->get();
        $value = Drama::where('id', $id)->with('country')->with('type')->with(['episode','episode.links','episode.backup',])->orderBy('id', 'desc')->first();
        return view('dashboard.dramaEps')->with('result', $value)->with("country", $country)->with("status", $status)->with("Type", $Type);
    }
    public function indexDetail($id)
    {

        //Cache::forget('Drama');
        if (!Drama::find($id)) {
            return abort('404');
        }

        //$value = Drama::with('country')->with('type')->with('eps')->orderBy('id', 'desc')->get();
        $value = Drama::where('id', $id)->with('country')->with('type')->with(['episode','episode.links','episode.backup',])->orderBy('id', 'desc')->first();
        $result = $this->GetTags($value);
        return response()->json($result);
    }

    public function get($id)
    {
        $data = Content::orderBy('id', 'desc')->with("links")->with("backup")->where('drama_id', $id)->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('links', function ($data) {
                $linkvideo = "";
                if ($data->links) {
                    $linkvideo .= '<div class="btn-group" role="group" aria-label="Command Action">';
                    foreach ($data->links as $link) {
                        $linkvideo .= ' <a href="https://drive.google.com/open?id='.$link->drive.'" target="_blank" class="btn btn-xs btn-success">'.$link->kualitas.'</a>';
                    }
                    $linkvideo .= '</div>';
                }
                return $linkvideo;
            })
            ->addColumn('backups', function ($data) {
                $linkvideo = "";
                if ($data->backup) {
                    $linkvideo .= '<div class="btn-group" role="group" aria-label="Command Action">';
                    foreach ($data->backup as $backup) {
                        $linkvideo .= ' <a href="https://drive.google.com/open?id='.$backup->f720p.'" target="_blank" class="btn btn-xs btn-success">'.$backup->title.'</a>';
                    }
                    $linkvideo .= '</div>';
                }
                return $linkvideo;
            })
            ->addColumn('action', function ($data) {
                if ($data->f720p) {
                    $f720p = '';
                } else {
                    $f720p = '<button type="button" name="url_720p" id="url_720p" data-clipboard-text="' . $data->url . '-720p" class="btn btn-xs btn-primary btncopy">Copy 720p</button>';
                }
                if ($data->f360p) {
                    $f360p = '';
                } else {
                    $f360p = '<button type="button" name="url_720p" id="url_720p" data-clipboard-text="' . $data->url . '-360p" class="btn btn-xs btn-primary btncopy">Copy 360p</button>';
                }
                return '<div class="btn-group" role="group" aria-label="Command Action">
                ' . $f360p . $f720p . '
                <a href="' . route("viewEps", $data->url) . '" target="_blank" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> show</a>
                <button type="button" id="btnShow" data-id="' . $data->id . '" data-drama_id="' . $data->drama_id . '" data-status="' . $data->status . '" data-title="' . $data->title . '" data-f720p="' . $data->f720p . '" data-f360p="' . $data->f360p . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></button>
                <button type="button" id="btnDelete" data-id="' . $data->id . '" data-drama_id="' . $data->drama_id . '" data-status="' . $data->status . '" data-title="' . $data->title . '" data-f720p="' . $data->f720p . '" data-f360p="' . $data->f360p . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></button></div>';
            })
            ->order(function ($data) {
                if (request()->has('id')) {
                    $data->orderBy('id', 'desc');
                }
            })
            ->rawColumns(['backups','links', 'action'])
            ->make(true);
    }
    public function Post(Request $request)
    {
        $checkPost = Content::where('title', $request->input("title"));
        if (!empty($request->input("id"))) {
            $dataContent = Content::find($request->input("id"));
            $dataContent->title = $request->input("title");
            $dataContent->drama_id = $request->input("drama_id");
            $dataContent->status = $request->input("status");
            $dataContent->f360p = $request->input("f360p");
            $dataContent->f720p = $request->input("f720p");
            $dataContent->save();
            $dataContentasd = "Update Success";
            return response()->json($dataContentasd, 201);
        }
        //totalEps
        if ($request->input("totalEps") < 2) {
            $dataContent = new Content;
            $dataContent->title = $request->input("title");
            $dataContent->url = $this->seoUrl($request->input("title"));
            $dataContent->drama_id = $request->input("drama_id");
            $dataContent->status = $request->input("status");
            $dataContent->f360p = $request->input("f360p");
            $dataContent->f720p = $request->input("f720p");
            $dataContent->save();
            if ($request->input('links')) {
                foreach ($request->input('links') as $a => $link) {
                    if (isset($link['id'])) {
                        $MetaLink = \App\masterlinks::find($link['id']);
                        $MetaLink->kualitas = $link['kualitas'];
                        $MetaLink->link = $link['link'];
                        $MetaLink->save();
                    } else {
                        $MetaLink = new MetaLink();
                        $MetaLink->kualitas = $link['kualitas'];
                        $MetaLink->link = $link['link'];
                        $dataContent->links()->save($MetaLink);
                    }
                }
            }
            $dataContentasd = "Insert Success";
            return response()->json($dataContentasd, 201);
        } else {
            $countEps = Content::where('drama_id', $request->input("drama_id"))->count();
            $countBatchEps = $request->input("totalEps");
            $title = $request->input("title");
            for ($i = 0; $i < $countBatchEps; $i++) {
                $dataContent = new Content;
                $j = $i + 1;
                $datacount = $countEps + $j;
                if ($datacount < 10) {
                    $datacount = "0" . $datacount;
                }

                $titles = $title . "" . $datacount;
                $dataContent->title = $titles;
                $dataContent->url = $this->seoUrl($titles);
                $dataContent->drama_id = $request->input("drama_id");
                $dataContent->status = $request->input("status");
                $dataContent->f360p = $request->input("f360p");
                $dataContent->f720p = $request->input("f720p");
                $dataContent->save();
                if ($request->input('links')) {
                    foreach ($request->input('links') as $a => $link) {
                        if (isset($link['id'])) {
                            $MetaLink = \App\masterlinks::find($link['id']);
                            $MetaLink->kualitas = $link['kualitas'];
                            $MetaLink->link = $link['link'];
                            $MetaLink->save();
                        } else {
                            $MetaLink = new MetaLink();
                            $MetaLink->kualitas = $link['kualitas'];
                            $MetaLink->link = $link['link'];
                            $content->links()->save($MetaLink);
                        }
                    }
                }
            }
            $dataContentasd = "Insert BatchEps Success ";
            return response()->json($dataContentasd, 201);
        }
    }
    public function Delete(Request $request, $id)
    {
        $dataContent = Content::find($request->input("id"));
        if (!is_null($dataContent)) {
            DB::table('contents')->where('id', '=', $request->input("id"))->delete();
            $dataContentasd = "Delete Success";
            return response()->json($dataContentasd, 201);
        }
        return response()->json("error Delete", 201);
    }
}
