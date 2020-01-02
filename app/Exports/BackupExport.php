<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BackupExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view(): View
    {
        
        // return response()->json($users);
        $users = \Cache::remember('users', 10, function () {
            return \App\Drama::with('country')->with('type')->with(['episode', 'episode.links', 'episode.backup'])->get();
        });
        return view('export',['pegawai' => $users]);
        
    }
    
}
