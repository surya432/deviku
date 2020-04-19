<?php

namespace App\Http\Controllers;

use App\GoogleDrivePlayer;
use Illuminate\Http\Request;

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
        return view('googledriveplayer.index')
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
        return $this->sendResponse($googleDrivePlayer->toArray(), 'googleDrivePlayer deleted successfully.');
    }
    public function getlist(){
        $data = GoogleDrivePlayer::where('status','active')->random(1)->first();
        return response()->json($data);
    } 
}
