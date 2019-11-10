<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Orchestra\Parser\Xml\Facade as XmlParser;
use Yajra\DataTables\Facades\DataTables;

class DrakorController extends Controller
{
    //
    use HelperController;
    function index()
    {
        return view('lastupdate.index');
    }
    function Data()
    {
        $xml = simplexml_load_string($this->viewsource("//123drakor.co/post-sitemap.xml"));

        $result = array();
        foreach ($xml as $datas) {
            array_push($result, $datas);
        }
        unset($result[0]);

        return Datatables::of($result)
            ->addColumn('date', function ($result) {
                return  date("d/m/Y", strtotime($result->lastmod));
            })

            ->make();
    }
}
