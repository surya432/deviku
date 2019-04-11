<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use App\Webfront;
use App\Content;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
use App\Drama;
class WebfrontsController extends Controller
{
    use HelperController;
    public function Index(){
        return view('webfronts.index');
    }
    public function get(){
        $data = Webfront::all();
        return Datatables::of($data)
        ->addColumn('action', function ($data) {
             return '<button type="button" id="btnShow" data-id="'.$data->id.'" data-site="'.$data->site.'" data-username="'.$data->username.'" data-password="'.$data->password.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
             <button type="button" id="btnDelete" data-id="'.$data->id.'" data-site="'.$data->site.'" data-username="'.$data->username.'" data-password="'.$data->password.'" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button>';
         })
        ->make(true);
    }
    public function Post(Request $request){
        if(!empty($request->input("id"))){
            $Webfront = Webfront::find($request->input("id"));
            $Webfront->username = Input::get("username");
            $Webfront->password = Input::get("password");
            $Webfront->site = Input::get("site");
            $Webfront->save();
            return response()->json($Webfront,201);

        }
        $Webfront = new Webfront;
        $Webfront->username = Input::get("username");
        $Webfront->password = Input::get("password");
        $Webfront->site = Input::get("site");
        $Webfront->save();
        return response()->json($Webfront,201);
    }

    public function Delete(Request $request){
        $Webfront= Webfront::find($request->input("id"));
        $Webfront->delete();
        return response()->json($Webfront,201);
    }
    public function seachdrama(Request $request){
        $site = Webfront::all();
        return view('webfronts.singkronweb')->with('site',$site);
    }
    public function postDrama(Request $request){
        $sites = Webfront::find($request->input('id'));
        $post = file_get_contents($sites->site.'/wp-json/wp/v2/posts/?search='.urlencode($request->input('seacrh')));
		$post  = json_decode($post,true);
		if(is_null($post)){
			return "error";
		}
		return view('webfronts.resultSearch')->with('url',$post);
    }
    public function singkronToWeb(Request $request, $idSite){
        $sites = Webfront::find($idSite);
        $header =  base64_encode($sites->username.":".$sites->password);   
        $drama_id = $request->input('drama_id');
        $idPost = $request->input('idPost');
/*         if(Cache::get('Drama')){
            $value =Cache::get('Drama')->where('id',$drama_id)->first();
        }else{
            $value = Drama::with('country')->with('type')->with('eps')->orderBy('id','desc')->get();
            Cache::forever('Drama',$value);
            $value = Cache::get('Drama')->where('id',$drama_id)->first();
        } */
        $value = Drama::where('id',$drama_id)->with('country')->with('type')->with('eps')->orderBy('id','desc')->first();
        $data = Content::orderBy('id','asc')->where('drama_id',$drama_id)->get();
        // return $this->getEmbed($data);
        $body  = "&title=".$value->title."&content=".$this->getEmbed($data);
        return $this->postWeb($sites->site, $idPost, $header,$body);
        //
    }
}
