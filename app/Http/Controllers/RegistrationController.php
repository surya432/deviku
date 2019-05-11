<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Yajra\DataTables\Facades\DataTables;

class RegistrationController extends Controller
{
    //
    public function register()
    {
        return view('authentication.register');
    }
    //adding for method Post
    public function registerPost(Request $request)
    {
        try {
            if (empty($request->input("id"))) {
                $user = Sentinel::registerAndActivate($request->only('email', 'password', 'first_name', 'last_name'));
                $role = Sentinel::findRoleBySlug($request->only('access'));
                $role->users()->attach($user);
                return response()->json(['success' => "Registrasi Sukses"], 201);
            } else {
                $user = Sentinel::findById($request->input("id"));
                $field = $request->only('email', 'first_name', 'last_name');

                if (!empty($request->input("password"))) {
                    $field = $request->all();
                }
                $uses = Sentinel::update($user, $field);

                if (empty($request->input("roles"))) {
                    $role = Sentinel::findRoleBySlug($request->only('access'));
                    $role->users()->attach($request->input("id"));
                }
                if ($request->input("roles") != $request->input("access")) {
                    $role = Sentinel::findRoleBySlug($request->only('roles'));
                    if ($role) {
                        $role->users()->detach($request->input("id"));
                    }
                }
                return response()->json(['success' => "Update Success"], 201);
            }
        } catch (ThrottlingException $e) {
            $message = $e->message;
            return response()->json(['alert' => $message], 500);
        }
    }
    public function test()
    {
        $user = Sentinel::inRole('editor');
        return response()->json($user, 201);
    }
    public function ListUser()
    {
        $roles = Sentinel::getRoleRepository()->get();
        return view('dashboard.allusers')->with('roles', $roles);
    }
    public function ListUserData()
    {
        $users = Sentinel::getUserRepository()->with('roles')->get();
        // return view('dashboard.allusers')->with('users', $users);
        return Datatables::of($users)
            ->addColumn('action', function ($users) {
                return '<button type="button" id="btnShow" data-id="' . $users->id . '" data-email="' . $users->email . '" data-fn="' . $users->first_name . '" data-ln="' . $users->last_name . '" data-access="' . $users->roles[0]->slug . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</button>
            <button type="button" id="btnDelete" data-id="' . $users->id . '" data-fn="' . $users->first_name . '" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</button>';
            })
            ->make(true);
    }
    public function DeleteUser(Request $request)
    {
        $user = Sentinel::findById($request->input('id'));

        $user->delete();
        return response()->json(['success' => "Delete Success"], 201);
    }
}
