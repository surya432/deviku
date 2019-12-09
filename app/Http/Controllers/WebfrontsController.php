<?php

namespace App\Http\Controllers;

use App\Content;
use App\Drama;
use App\Webfront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;

class WebfrontsController extends Controller
{
    use HelperController;
    public function Index()
    {
        return view('webfronts.index');
    }
    public function get()
    {
        $data = Webfront::all();
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<button type="button" id="btnShow" data-id="' . $data->id . '" data-site="' . $data->site . '" data-username="' . $data->username . '" data-password="' . $data->password . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
             <button type="button" id="btnDelete" data-id="' . $data->id . '" data-site="' . $data->site . '" data-username="' . $data->username . '" data-password="' . $data->password . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button>';
            })
            ->make(true);
    }
    public function Post(Request $request)
    {
        if (!empty($request->input("id"))) {
            $Webfront = Webfront::find($request->input("id"));
            $Webfront->username = Input::get("username");
            $Webfront->password = Input::get("password");
            $Webfront->site = Input::get("site");
            $Webfront->save();
            return response()->json($Webfront, 201);
        }
        $Webfront = new Webfront;
        $Webfront->username = Input::get("username");
        $Webfront->password = Input::get("password");
        $Webfront->site = Input::get("site");
        $Webfront->save();
        return response()->json($Webfront, 201);
    }

    public function Delete(Request $request)
    {
        $Webfront = Webfront::find($request->input("id"));
        $Webfront->delete();
        return response()->json($Webfront, 201);
    }
    public function seachdrama(Request $request)
    {
        $site = Webfront::all();
        return view('webfronts.singkronweb')->with('site', $site);
    }
    public function postDrama(Request $request)
    {
        $sites = Webfront::find($request->input('id'));
        $post = $this->viewsource($sites->site . '/wp-json/wp/v2/posts/?search=' . urlencode($request->input('seacrh')));
        $post = json_decode($post, true);
        if (is_null($post)) {
            return "error";
        }
        return view('webfronts.resultSearch')->with('url', $post);
    }
    public function singkronToWeb(Request $request, $idSite)
    {
        $sites = Webfront::find($idSite);
        $header = base64_encode($sites->username . ":" . $sites->password);
        $drama_id = $request->input('drama_id');
        $idPost = $request->input('idPost');
        $data = Content::orderBy('id', 'asc')->where('drama_id', $drama_id)->get();

        $value = Drama::where('id', $drama_id)->with('country')->with('type')->with('eps')->orderBy('id', 'desc')->first();
        $status = $this->getTermsId($sites->site, "status", $value->status);
        $client = new \GuzzleHttp\Client(['verify' => false, 'headers' => [
            "Authorization" => " Basic " . $header,
        ]]);
        $body = [
            'json' => [
                'title' => $value->title,
                'content' => $this->getEmbed($data),
                'terms' => array(
                    "status" => $status,
                ),
            ],
        ];
        $response = $client->post($sites->site . "/wp-json/wp/v2/posts/" . $idPost, $body);
        return $response->getBody()->getContents();
        // // return $this->getEmbed($data);
        // $body = "&title=" . $value->title . "&content=" . $this->getEmbed($data);
        // return $this->postWeb($sites->site, $idPost, $header, $body);
    }
    public function asiawiki(Request $request)
    {
        $data = $this->getDetailDrama($request->input("source"));
        return $data;
    }
    public function _mime_content_type($filename)
    {
        $result = new finfo();

        if (is_resource($result) === true) {
            return $result->file($filename, FILEINFO_MIME_TYPE);
        }

        return false;
    }
    public function preCreate(Request $request)
    {
        $sites = Webfront::find($request->input('siteid'));
        $header = base64_encode($sites->username . ":" . $sites->password);
        $file = $request->file('imageupload');
        $filename = $file->getClientOriginalName();
        $path = \Storage::disk('public')->path($filename);
        if (!\Storage::disk('public')->has($filename)) {
            \Storage::disk('public')->put($file->getClientOriginalName(), \File::get($file));
        }
        $image = $this->upload_image($sites->site, $header, $path, $file->getClientOriginalExtension(), $filename, $file->getSize());
        $tag = $request->input('post_tag');
        $status = $this->getTermsId($sites->site, "status", $request->input('status'));
        $genre = $this->getTermsId($sites->site, "genre", $request->input('genre'));
        $actor = $this->getTermsId($sites->site, "actor", $request->input('cast'));
        $content = $request->input('iframe');
        $title = $request->input('titleDetail');
        $category = $request->input('categories');
        $country = $this->getTermsId($sites->site, "country", $request->input('country'));
        $client = new \GuzzleHttp\Client(['verify' => false, 'headers' => [
            "Authorization" => " Basic " . $header,
        ]]);
        $body = [
            'json' => [
                'title' => $title,
                'content' => $content,
                'status' => "publish",
                'format' => "video",
                'featured_media' => intval($image['id']),
                'terms' => array(
                    "post_tag" => $tag,
                    "country" => $country,
                    "actor" => $actor,
                    "genre" => $genre,
                    "status" => $status,
                    "categories" => $category,
                ),
            ],
        ];
        $response = $client->post($sites->site . "/wp-json/wp/v2/posts", $body);
        return $response->getBody()->getContents();
    }
    private function getTermsId($siteUrl, $terms, $s)
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->post($siteUrl . '/wp-json/wp/v2/search-terms', [
            'form_params' => [
                'term' => $terms,
                's' => $s,
            ],
            'headers' => [
                // "Authorization"=>" Basic " . $header,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data, true);
    }
    public function preCreatePost(Request $request, $idDrama)
    {
        $value = Drama::where('id', $idDrama)->with('country')->with('type')->with('eps')->orderBy('id', 'desc')->first();
        $result = $this->GetTags($value);
        $site = Webfront::all();
        return view('dashboard.createnewpostwp', compact('result', 'site'));
    }

    public function upload_image($sites, $header, $file, $mime, $title, $size)
    {
        $file = file_get_contents(public_path("uploads/" . $title));
        $url = $sites . '/wp-json/wp/v2/media/';
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'Content-Disposition: form-data; filename="' . basename(public_path("uploads/" . $title)) . '"',
        //     'Authorization: Basic ' . $header,
        //     'Accept:application/json',
        // ]);
        // $data = curl_exec($ch);
        // $err = curl_error($ch);
        // curl_close($ch);
        // if ($err) {
        //     return "";
        // } else {
        // return $data;
        // }
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->post($sites . '/wp-json/wp/v2/media', [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $file,
                    'filename' => basename(public_path("uploads/" . $title))
                ],
            ],
            'headers' => [
                "Authorization"=>" Basic " . $header,
                "Content-Disposition"=>'attachment; filename="' . basename(public_path("uploads/" . $title)) . '"',
            ],
            
        ]);
        $data = $response->getBody()->getContents();
        return json_decode($data, true);
    }
}
