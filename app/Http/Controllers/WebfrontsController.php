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

        $value = Drama::where('id', $drama_id)->with('country')->with('type')->with('eps')->orderBy('id', 'desc')->first();
        $data = Content::orderBy('id', 'asc')->where('drama_id', $drama_id)->get();
        // return $this->getEmbed($data);
        $body = "&title=" . $value->title . "&content=" . $this->getEmbed($data);
        return $this->postWeb($sites->site, $idPost, $header, $body);
    }
    public function asiawiki(Request $request)
    {
        $data = $this->getDetailDrama($request->input("source"));
        return $data;
    }
    public function preCreate(Request $request)
    {
        $sites = Webfront::find($request->input('siteid'));
        $header = base64_encode($sites->username . ":" . $sites->password);
        $tag = $request->input('post_tag');

        $status = $this->getTermsId($sites->site, "status", $request->input('status'));
        $genre = $this->getTermsId($sites->site, "genre", $request->input('genre'));
        $actor = $this->getTermsId($sites->site, "actor", $request->input('cast'));
        $contetn = $request->input('iframe');
        $title = $request->input('titleDetail');
        $category = $request->input('categories');
        $country = $this->getTermsId($sites->site, "country", $request->input('country'));
        $client = new \GuzzleHttp\Client(['verify' => false, 'headers' => [
            "Authorization" => " Basic " . $header,
        ]]);
        $body = [
            'form_params' => [
                'title' => $title,
                'content' => $contetn,
                'status' => 'pending',
                'format' => "video",
                'terms' => [
                    "post_tag" => $tag,
                    "country" => $country,
                    "actor" => $actor,
                    "genre" => $genre,
                    "status" => $status,
                    "categories" => $category,
                ],
            ],
            'timeout' => 500,
        ];
        //return str_replace(array('"[', ']"'), array("[", "]"), $body);
        $response = $client->post($sites->site . "/wp-json/wp/v2/posts", $body);
        return $response->getBody()->getContents();
    }
    private function createnewPost($siteUrl, $body, $header)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $siteUrl . "/wp-json/wp/v2/posts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . $header,
                "Postman-Token: ef0edd2c-2ab4-4dff-b8fb-b1fc2e6b607a",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            return "";
        } else {
            return $response;
        }
    }
    private function getTermsId($siteUrl, $terms, $s)
    {
        // $client = new \GuzzleHttp\Client(['verify' => false]);

        // $response = $client->post($siteUrl . '/wp-json/wp/v2/search-terms', [
        //     'form_params' => [
        //         'term' => $terms,
        //         's' => $s,
        //     ],
        //     'headers' => [
        //         // "Authorization"=>" Basic " . $header,

        //         'Content-Type' => 'application/x-www-form-urlencoded',
        //     ],
        // ]);
        // return $response->getBody()->getContents();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $siteUrl . '/wp-json/wp/v2/search-terms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "term=$terms&s=$s",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: 21",
                "Content-Type: application/x-www-form-urlencoded",
                "Cookie: __cfduid=d4bec8c07c760fa4ae22097ddf91416241574169209",
                "Host: nontonindrama.com",
                "Postman-Token: 831726b7-7563-4baf-94be-2bbb28bf2f9a,5602c019-6c98-49df-8f90-d5056d8eae1e",
                "User-Agent: PostmanRuntime/7.20.1",
                "cache-control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "";
        } else {
            return $response;
        }
    }
    public function preCreatePost(Request $request, $idDrama)
    {
        $value = Drama::where('id', $idDrama)->with('country')->with('type')->with('eps')->orderBy('id', 'desc')->first();
        $result = $this->GetTags($value);
        $site = Webfront::all();
        return view('dashboard.createnewpostwp', compact('result', 'site'));
    }
}
