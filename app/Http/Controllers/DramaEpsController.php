<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use App\Drama;
use App\Content;
use Cache;
use App\Type;
use DB;
use Yajra\DataTables\Facades\DataTables;
class DramaEpsController extends Controller
{
    //
    use HelperController;
    public function index($id){

        //Cache::forget('Drama');
        $checkPost = Drama::find($id);
        if(is_null($checkPost)){
            return abort('404');
        }
        if(Cache::get('Drama')){
            $value =Cache::get('Drama')->where('id',$id)->first();
        }else{
            $value = Drama::with('country')->with('type')->with('eps')->orderBy('id','desc')->get();
            Cache::forever('Drama',$value);
            $value = Cache::get('Drama')->where('id',$id)->first();
        }
        return view('dashboard.dramaEps')->with('result',$value);

    }
    public function indexDetail($id){

        //Cache::forget('Drama');
        if(!Drama::find($id)){
            return abort('404');
        }
        if(Cache::get('Drama')){
            $value =Cache::get('Drama')->where('id',$id)->first();
        }else{
            $value = Drama::with('country')->with('type')->with('eps')->orderBy('id','desc')->get();
            Cache::forever('Drama',$value);
            $value = Cache::get('Drama')->where('id',$id)->first();
        }
        $result = $this->GetTags($value);
        return response()->json($result);

    }
    public function get($id){
        $data = Content::orderBy('id','desc')->where('drama_id',$id)->get();
        return Datatables::of($data)
            ->addColumn('f360ps', function ($data) {
                if($data->f360p){
                    return 'true';
                }else{
                    return 'false';
                } 
            })
            ->addColumn('f720ps', function ($data) {
                if($data->f720p){
                    return 'true';
                }else{
                    return 'false';
                }
            })
            ->addColumn('action', function ($data) {
                if($data->f720p){
                    $f720p =  '';
                }else{
                    $f720p = '<input type="text" name="url_720p" id="url_720p" value="'.$data->url.'-720p">';
                }
                if($data->f360p){
                    $f360p =  '';
                }else{
                    $f360p = '<input type="text" name="url_720p" id="url_720p" value="'.$data->url.'-360p">';
                }
                return '<div class="btn-group" role="group" aria-label="Command Action">
                '.$f360p.$f720p.'
                <a href="'.route("viewEps",$data->url).'" target="_blank" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-eye-open"></i> show</a>
                <button type="button" id="btnShow" data-id="'.$data->id.'" data-drama_id="'.$data->drama_id.'" data-status="'.$data->status.'" data-title="'.$data->title.'" data-f720p="'.$data->f720p.'" data-f360p="'.$data->f360p.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
                <button type="button" id="btnDelete" data-id="'.$data->id.'" data-drama_id="'.$data->drama_id.'" data-status="'.$data->status.'" data-title="'.$data->title.'" data-f720p="'.$data->f720p.'" data-f360p="'.$data->f360p.'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button></div>';
            })
            ->order(function ($data) {
                if (request()->has('id')) {
                    $data->orderBy('id', 'desc');
                }

            })
            ->make(true);
    }
    public function Post(Request $request){
	$checkPost = Content::where('title',$request->input("title"));	
        if(!empty($request->input("id"))){
            $dataContent = Content::find($request->input("id"));
            $dataContent->title = $request->input("title");
            $dataContent->drama_id = $request->input("drama_id");
            $dataContent->status = $request->input("status");
            $dataContent->f360p = $request->input("f360p");
            $dataContent->f720p = $request->input("f720p");
            $dataContent->save();
            $dataContentasd = "Update Success";
            return response()->json($dataContentasd,201);
        }
        $dataContent = new Content;
        $dataContent->title = $request->input("title");
        $dataContent->url = $this->seoUrl($request->input("title"));
        $dataContent->drama_id = $request->input("drama_id");
        $dataContent->status = $request->input("status");
        $dataContent->f360p = $request->input("f360p");
        $dataContent->f720p = $request->input("f720p");
        $dataContent->save();
        $dataContentasd = "Insert Success";
        return response()->json($dataContentasd,201);
    }
    public function Delete(Request $request,$id){
        $dataContent= Content::find($request->input("id"));
        if(!is_null($dataContent)){
            DB::table('contents')->where('id','=', $request->input("id") )->delete();
            $dataContentasd = "Delete Success";
            return response()->json($dataContentasd,201);
        }
        return response()->json("error Delete",201);
    }

}
