<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Yajra\DataTables\Facades\DataTables;

class RolesController extends Controller
{
    //
    public function index()
    {
        return view('dashboard.roles');
    }
    public function rolesData()
    {
        $roles = Sentinel::getRoleRepository()->get();
        return Datatables::of($roles)
            ->addColumn('action', function ($roles) {
                return '<button type="button" id="btnShow" data-id="' . $roles->id . '" data-name="' . $roles->name . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button><button type="button" id="btnDelete" data-id="' . $roles->id . '" data-name="' . $roles->name . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button>';
            })
            ->make(true);
    }
    public function RolesPost(Request $request)
    {
        $role = Sentinel::getRoleRepository()->createModel()->create([
            'name' => $request->input('name'),
            'slug' => strtolower($request->input('name')),
        ]);
        return response()->json(['success' => "Added Success"], 201);
    }
    public function RolesDelete(Request $request)
    {
        $role = Sentinel::findRoleById($request->input('id'));
        $role->delete();
        return response()->json(['success' => "Delete Success"], 201);
    }
}
