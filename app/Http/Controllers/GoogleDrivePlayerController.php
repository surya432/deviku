<?php

namespace App\Http\Controllers;

use App\GoogleDrivePlayer;
use Illuminate\Http\Request;

use Validator;
use DB;

use Yajra\Datatables\Datatables;

class GoogleDrivePlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('googledriveplayer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view("googledriveplayer.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|unique:google_drive_players,email',
            'cookiestext' => 'required',
            'status' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $product = \App\GoogleDrivePlayer::create($input);
        return $this->sendResponse($product->toArray(), 'created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GoogleDrivePlayer  $googleDrivePlayer
     * @return \Illuminate\Http\Response
     */
    public function show(GoogleDrivePlayer $googleDrivePlayer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GoogleDrivePlayer  $googleDrivePlayer
     * @return \Illuminate\Http\Response
     */
    public function edit(GoogleDrivePlayer $googleDrivePlayer)
    {
        //
        return view("googledriveplayer.edit", compact('googleDrivePlayer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GoogleDrivePlayer  $googleDrivePlayer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GoogleDrivePlayer $googleDrivePlayer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GoogleDrivePlayer  $googleDrivePlayer
     * @return \Illuminate\Http\Response
     */
    public function destroy(GoogleDrivePlayer $googleDrivePlayer)
    {
        $googleDrivePlayer->delete();
        if ($googleDrivePlayer) {
            DB::table('google_drive_players')->where('id', '=', $googleDrivePlayer->id)->delete();
            return $this->sendResponse($googleDrivePlayer->toArray(), 'googleDrivePlayer deleted successfully.');
        }
        return response()->json($googleDrivePlayer);
    }
    public function getlist()
    {
        $data = GoogleDrivePlayer::where('status', 'active')->inRandomOrder()->first();
        return response()->json($data);
    }
    public function jsonDataTable()
    {
        $query = \App\GoogleDrivePlayer::orderBy('id', 'desc')->get();
        //$query mempunyai isi semua data di table users, dan diurutkan dari data yang terbaru
        return Datatables::of($query)
            //$query di masukkan kedalam Datatables
            ->addColumn('action', function ($q) {
                //Kemudian kita menambahkan kolom baru , yaitu "action"
                return view('links', [
                    //Kemudian dioper ke file links.blade.php
                    'model'      => $q,
                    'url_edit'   => route('cookies.edit', $q->id),
                    'url_hapus'  => route('cookies.destroy', $q->id),
                    // 'url_detail' => route('mirrorkey.show', $q->id),
                ]);
            })
            ->addIndexColumn()
            // ->rawColumns(['other-columns'])
            ->make(true);
    }
}
