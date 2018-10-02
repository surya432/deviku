<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
class LoginController extends Controller
{
    //
    public function login(){

        return view('authentication.login');
    }
    public function loginPost(request $request){
        try {
            $rememberMe = false;
            if(isset($request->remember_me)) {
                $rememberMe = true;
            }
            if(Sentinel::authenticate($request->all(),$rememberMe)){
                // $slug = Sentinel::getUser()->roles()->first()->slug;
                // if($slug =='admin'){
                    return response()->json(['redirect'=>"/admin"]);
                // }else{
                //     return response()->json(['redirect'=>"/editor"]);
                // }
            }else{
                return response()->json(['alert'=>"Email Or Password Wrong!!"],500);
            }
        }catch(ThrottlingException $e){
            $delay = $e->getDelay();
            return response()->json(['alert'=>"Your are Banned For $delay Seconds"],500);

        }catch(NotActivatedException $e){
            return response()->json(['alert'=>"Your are Deactive"],500);
        }
    }
    public function logout(){
        Sentinel::logout();
        return redirect('/login');
    }
}
