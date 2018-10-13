<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return abort(404);
});
Route::get('/register', function (){
    return abort(404);
});
Route::get('/embed/{url}', ['as'=>'viewEps','uses'=>'EmbedController@Index']);
Route::get('/ajax/embed/{url}', function(){
    return abort(404);
});
Route::post('/ajax/embed/{url}', ['as'=>'ajaxEps','uses'=>'EmbedController@getDetail']);
Route::get('/login', ['as'=>'login','uses'=>'LoginController@login']);
Route::post('/admin/webfront/singkron', ['as'=>'webfrontSingkronpost','uses'=>'WebfrontsController@postDrama']);
Route::post('/login',['as'=>'loginPost','uses'=>'LoginController@loginPost']);

Route::group(['middleware' => ['web']], function(){
    Route::post('/admin/logout',['as'=>'logout','uses'=>'LoginController@logout']);

});

Route::group(['middleware' => ['admin','web']], function(){
    Route::get('/admin', ['as'=>'admin','uses'=>'DashboardController@index']);
    
    Route::post('/admin/users',['as'=>'users.add','uses'=>'RegistrationController@registerPost']);
    Route::delete('/admin/users',['as'=>'users.delete','uses'=>'RegistrationController@DeleteUser']);
    Route::get('/admin/users/', ['as'=>'users','uses'=>'RegistrationController@ListUser']);
    Route::get('/admin/users-data', ['as'=>'users.getData','uses'=>'RegistrationController@ListUserData']);
    
    Route::get('/admin/users/roles/', ['as'=>'users.roles','uses'=>'RolesController@index']);
    Route::get('/admin/users/role-data', ['as'=>'users.roleData','uses'=>'RolesController@rolesData']);
    Route::post('/admin/users/role/', ['as'=>'users.RolesPost','uses'=>'RolesController@RolesPost']);
    Route::delete('/admin/users/role/', ['as'=>'users.RolesDelete','uses'=>'RolesController@RolesDelete']);
    
    Route::get('/admin/setting/', ['as'=>'setting.get','uses'=>'SettingController@index']);
    Route::get('/admin/setting-data', ['as'=>'setting.getData','uses'=>'SettingController@get']);
    Route::post('/admin/setting/', ['as'=>'setting.postData','uses'=>'SettingController@post']);
    
    Route::get('/admin/gmail/', ['as'=>'gmail','uses'=>'GmailController@Index']);
    Route::get('/admin/gmail-data', ['as'=>'gmailData','uses'=>'GmailController@Data']);
    Route::post('/admin/gmail', ['as'=>'gmailPost','uses'=>'GmailController@Post']);
    Route::get('/admin/gmail/delete', ['as'=>'gmailDelete','uses'=>'GmailController@Delete']);

    Route::get('/admin/country', ['as'=>'country','uses'=>'CountryController@Index']);
    Route::get('/admin/country-data', ['as'=>'countryData','uses'=>'CountryController@Data']);
    Route::post('/admin/country', ['as'=>'countryPost','uses'=>'CountryController@Post']);
    Route::delete('/admin/country', ['as'=>'countryDelete','uses'=>'CountryController@Delete']);

    Route::get('/admin/drama', ['as'=>'drama','uses'=>'DramaController@Index']);
    Route::get('/admin/drama-data', ['as'=>'dramaData','uses'=>'DramaController@get']);
    Route::Post('/admin/drama', ['as'=>'dramaPost','uses'=>'DramaController@Post']);
    Route::get('/admin/drama/delete', ['as'=>'dramaDelete','uses'=>'DramaController@Delete']);

    Route::get('/admin/type', ['as'=>'type','uses'=>'TypeController@Index']);
    Route::get('/admin/ajax/type-data', ['as'=>'typeData','uses'=>'TypeController@get']);
    Route::Post('/admin/type', ['as'=>'typePost','uses'=>'TypeController@Post']);
    Route::delete('/admin/type', ['as'=>'typeDelete','uses'=>'TypeController@Delete']);
    
    Route::get('/admin/drama/{id}/eps/', ['as'=>'eps','uses'=>'DramaEpsController@Index']);
    Route::get('/admin/drama/{id}/eps/data"', ['as'=>'epsData','uses'=>'DramaEpsController@get']);
    Route::get('/admin/drama/{id}/eps/detail"', ['as'=>'epsDetail','uses'=>'DramaEpsController@indexDetail']);
    Route::Post('/admin/drama/{id}/eps/', ['as'=>'epsPost','uses'=>'DramaEpsController@Post']);
    Route::get('/admin/drama/{id}/eps/delete', ['as'=>'epsDelete','uses'=>'DramaEpsController@Delete']);

    Route::get('/admin/drive/content/{id}', ['as'=>'driveEps','uses'=>'GDController@singkron']);
    Route::get('/admin/drive/drama/{id}', ['as'=>'driveDrama','uses'=>'GDController@foldersingkron']);
   

    Route::get('/admin/webfront/', ['as'=>'webfront','uses'=>'WebfrontsController@index']);
    Route::get('/admin/webfront-data/', ['as'=>'webfrontGet','uses'=>'WebfrontsController@get']);
    Route::delete('/admin/webfront-data/', ['as'=>'webfrontDelete','uses'=>'WebfrontsController@delete']);
    Route::post('/admin/webfront-data/', ['as'=>'webfrontPost','uses'=>'WebfrontsController@post']);
    Route::get('/admin/webfront/singkron', ['as'=>'webfrontSingkron','uses'=>'WebfrontsController@seachdrama']);
    Route::post('/admin/webfront/singkron/{idSite}', ['as'=>'singkronToWeb','uses'=>'WebfrontsController@singkronToWeb']);
});
