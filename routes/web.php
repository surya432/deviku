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
Route::get('/register', function () {
    return abort(404);
});
Route::get('/downExport', function () {
    return Excel::download(new \App\Exports\BackupExport, 'backup.xlsx');
});
Route::get('/test/{id}', function ($id) {
    $users = \App\Drama::with('country')->with('type')->with(['episode', 'episode.links', 'episode.backup'])->whereYear('created_at', $id);
    // return response()->json($users);
    return view('export',['pegawai' => $users]);
});

Route::get('/generator/backup', ['as' => 'backup', 'uses' => 'BackupController@index']);
Route::get('/generator/mirror', ['as' => 'getMirrorAlternatif', 'uses' => 'BackupController@getMirrorAlternatif']);
Route::get('/generator/links', ['as' => 'viewEpsCoeg', 'uses' => 'BackupController@changeMaster']);
Route::get('/embed/{url}', ['as' => 'viewEps', 'uses' => 'EmbedController@Index']);
Route::get('/ajax/videos/{url}', function () {
    return abort(404);
});
Route::get('/drive/cookies', ['as' => 'googleDriveCookies', 'uses' => 'GoogleDrivePlayerController@getlist']);
Route::get('/detail/drama', ['as' => 'dramacurl', 'uses' => 'WebfrontsController@asiawiki']);
Route::post('/ajax/videos/{url}', ['as' => 'ajaxEps', 'uses' => 'EmbedController@getDetail']);
Route::get('/login', ['as' => 'login', 'uses' => 'LoginController@login']);
Route::post('/login', ['as' => 'loginPost', 'uses' => 'LoginController@loginPost']);
Route::post('/gmail/update', ['as' => 'gmailPostUpdate', 'uses' => 'GmailController@Post']);
Route::post('/gmail/post', ['as' => 'gmailPostaddEmail', 'uses' => 'GmailController@Post']);
Route::get('/deletegd', ['as' => 'gmailPostUpdate', 'uses' => 'BackupController@deletegdFromDB']);
Route::get('/testGd', ['as' => 'testGd', 'uses' => 'BackupController@testgd']);
Route::get('/singkron/drama/{id}', ['as' => 'singkrons', 'uses' => 'GDController@syncFolder']);
Route::get('/proxyDrive', ['as' => 'ProxyDriveIndex', 'uses' => 'ProxyDriveController@index']);
Route::get('/proxyDrive/{id}', ['as' => 'ProxyDriveContents', 'uses' => 'ProxyDriveController@getBrokenLink']);
Route::get('/proxyDriveps1', ['as' => 'ProxyDriveContentsps1', 'uses' => 'ProxyDriveController@fileBrokenLinkPs1']);
Route::group(['middleware' => ['web']], function () {
    Route::post('/admin/logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);
});

Route::group(['middleware' => ['admin', 'web']], function () {
    Route::get('/admin/drive/deleteall/', ['as' => 'driveDramaDelete', 'uses' => 'EmbedController@deletegdbydate']);

    Route::get('/admin', ['as' => 'admin', 'uses' => 'DashboardController@index']);
    Route::get('/admin/list-update', ['as' => 'dramaDataUpdate', 'uses' => 'DashboardController@get']);
    Route::get('/admin/viu', ['as' => 'viudownloader', 'uses' => 'ViuController@index']);
    Route::post('/admin/viu', ['as' => 'viugetdata', 'uses' => 'ViuController@getData']);

    Route::get('/admin/folder', ['as' => 'singkronFolder', 'uses' => 'GDController@singkronFolder']);
    Route::get('/admin/token', ['as' => 'singkronFolderToken', 'uses' => 'GDController@AdminToken']);
    Route::get('/admin/googledrive/create-folder', ['as' => 'createFolderDrive', 'uses' => 'GDController@createFolderDrive']);

    Route::post('/admin/users', ['as' => 'users.add', 'uses' => 'RegistrationController@registerPost']);
    Route::get('/admin/users/laporan', ['as' => 'users.laporan', 'uses' => 'UsersController@index']);
    Route::get('/admin/users/getlaporan', ['as' => 'users.getlaporan', 'uses' => 'UsersController@getlaporan']);
    Route::post('/admin/users/addlaporan', ['as' => 'users.addlaporan', 'uses' => 'UsersController@addlaporan']);
    Route::delete('/admin/users', ['as' => 'users.delete', 'uses' => 'RegistrationController@DeleteUser']);
    Route::get('/admin/users/', ['as' => 'users', 'uses' => 'RegistrationController@ListUser']);
    Route::get('/admin/users-data', ['as' => 'users.getData', 'uses' => 'RegistrationController@ListUserData']);
    Route::get('/admin/users/roles/', ['as' => 'users.roles', 'uses' => 'RolesController@index']);
    Route::get('/admin/users/role-data', ['as' => 'users.roleData', 'uses' => 'RolesController@rolesData']);
    Route::post('/admin/users/role/', ['as' => 'users.RolesPost', 'uses' => 'RolesController@RolesPost']);
    Route::delete('/admin/users/role/', ['as' => 'users.RolesDelete', 'uses' => 'RolesController@RolesDelete']);
    Route::get('/admin/setting/', ['as' => 'setting.get', 'uses' => 'SettingController@index']);
    Route::get('/admin/setting-data', ['as' => 'setting.getData', 'uses' => 'SettingController@get']);
    Route::post('/admin/setting/', ['as' => 'setting.postData', 'uses' => 'SettingController@post']);

    Route::get('/admin/gmail/', ['as' => 'gmail', 'uses' => 'GmailController@Index']);
    Route::get('/admin/gmail-data', ['as' => 'gmailData', 'uses' => 'GmailController@Data']);
    Route::post('/admin/gmail', ['as' => 'gmailPost', 'uses' => 'GmailController@Post']);
    Route::get('/admin/gmail/delete', ['as' => 'gmailDelete', 'uses' => 'GmailController@Delete']);
    Route::get('/admin/gmail/token', ['as' => 'gmailtoken', 'uses' => 'GmailController@getToken']);
    Route::get('/admin/gmail/token/admin', ['as' => 'gmailtoken', 'uses' => 'GmailController@getTokenAdmin']);

    Route::get('/admin/country', ['as' => 'country', 'uses' => 'CountryController@Index']);
    Route::get('/admin/country-data', ['as' => 'countryData', 'uses' => 'CountryController@Data']);
    Route::post('/admin/country', ['as' => 'countryPost', 'uses' => 'CountryController@Post']);
    Route::delete('/admin/country', ['as' => 'countryDelete', 'uses' => 'CountryController@Delete']);

    Route::get('/admin/drama', ['as' => 'drama', 'uses' => 'DramaController@Index']);
    Route::get('/admin/drama-data', ['as' => 'dramaData', 'uses' => 'DramaController@get']);
    Route::Post('/admin/drama', ['as' => 'dramaPost', 'uses' => 'DramaController@Post']);
    Route::get('/admin/drama/delete', ['as' => 'dramaDelete', 'uses' => 'DramaController@Delete']);

    Route::get('/admin/type', ['as' => 'type', 'uses' => 'TypeController@Index']);
    Route::get('/admin/ajax/type-data', ['as' => 'typeData', 'uses' => 'TypeController@get']);
    Route::Post('/admin/type', ['as' => 'typePost', 'uses' => 'TypeController@Post']);
    Route::delete('/admin/type', ['as' => 'typeDelete', 'uses' => 'TypeController@Delete']);

    Route::get('/admin/drama/{id}/eps/', ['as' => 'eps', 'uses' => 'DramaEpsController@Index']);
    Route::get('/admin/drama/eps/{id}', ['as' => 'epsEdit', 'uses' => 'DramaEpsController@edit']);
    Route::get('/admin/drama/{id}/eps/data', ['as' => 'epsData', 'uses' => 'DramaEpsController@get']);
    Route::get('/admin/drama/{id}/eps/detail', ['as' => 'epsDetail', 'uses' => 'DramaEpsController@indexDetail']);
    Route::get('/admin/drama/{id}/detail', ['as' => 'DetailExt', 'uses' => 'DramaEpsController@Detail']);
    Route::Post('/admin/drama/{id}/eps/', ['as' => 'epsPost', 'uses' => 'DramaEpsController@Post']);
    Route::delete('/admin/drama/{id}/eps/delete', ['as' => 'epsDelete', 'uses' => 'DramaEpsController@Delete']);

    Route::get('/admin/drive/content/{id}', ['as' => 'driveEps', 'uses' => 'GDController@syncFolder']);
    Route::get('/admin/drive/drama/{id}', ['as' => 'driveDrama', 'uses' => 'GDController@foldersingkron']);
    Route::post('/admin/webfront/singkron', ['as' => 'webfrontSingkronpost', 'uses' => 'WebfrontsController@postDrama']);

    Route::get('/admin/brokenlinks/', ['as' => 'brokenlinksIndex', 'uses' => 'BrokenLinkController@index']);
    Route::get('/admin/brokenlinks/table', ['as' => 'brokenlinksIndexTables', 'uses' => 'BrokenLinkController@brokenlinksIndexTables']);
    Route::get('/admin/brokenlinks/detail/{id}', ['as' => 'DetailBrokenLinks', 'uses' => 'BrokenLinkController@DetailBrokenLinks']);
    Route::get('/admin/brokenlinks/details/{id}', ['as' => 'DetailBrokenLink', 'uses' => 'BrokenLinkController@DetailBrokenLink']);
    Route::get('/admin/brokenlinks/setfixed/', ['as' => 'SetEpsFixed', 'uses' => 'BrokenLinkController@SetEpsFixed']);

    Route::get('/admin/lastupdate/', ['as' => 'DrakorUpdateIndex', 'uses' => 'DrakorController@index']);
    Route::get('/admin/lastupdate/data', ['as' => 'DrakorUpdateData', 'uses' => 'DrakorController@Data']);

    Route::get('/admin/webfront/', ['as' => 'webfront', 'uses' => 'WebfrontsController@index']);
    Route::get('/admin/webfront/new/{idDrama}', ['as' => 'webfrontAddPost', 'uses' => 'WebfrontsController@preCreatePost']);
    Route::get('/admin/webfront-data/', ['as' => 'webfrontGet', 'uses' => 'WebfrontsController@get']);
    Route::delete('/admin/webfront-data/', ['as' => 'webfrontDelete', 'uses' => 'WebfrontsController@delete']);
    Route::post('/admin/webfront-data/', ['as' => 'webfrontPost', 'uses' => 'WebfrontsController@post']);
    Route::post('/admin/webfront-data/post', ['as' => 'preCreate', 'uses' => 'WebfrontsController@preCreate']);
    Route::get('/admin/webfront/singkron', ['as' => 'webfrontSingkron', 'uses' => 'WebfrontsController@seachdrama']);
    Route::post('/admin/webfront/singkron/{idSite}', ['as' => 'singkronToWeb', 'uses' => 'WebfrontsController@singkronToWeb']);

    //New Feture
    Route::get('/admin/mirror/datatabel', 'MirrorKeyController@json')->name('Apimirrorkeyjson');
    Route::get('/admin/master-mirror', 'MasterMirrorController@index')->name('masterMirrorController');
    Route::get('/admin/datatables/master-mirror', 'MasterMirrorController@create')->name('masterMirrorCreate');
    Route::post('/admin/form/master-mirror-create', 'MasterMirrorController@store')->name('ApiMasterMirrorStore');
    Route::patch('/admin/form/master-mirror-update/{id}', 'MasterMirrorController@update')->name('ApiMasterMirrorupdate');
    Route::get('/admin/form/master-mirror-edit/{id}', 'MasterMirrorController@edit')->name('master-mirror.edit');
    Route::delete('/admin/form/master-mirror-delete', 'MasterMirrorController@destroy')->name('master-mirror.destroy');
    Route::get('/admin/apikey', 'MirrorkeyController@index')->name('MirrorkeyController');
    Route::get('/admin/datatables/master-apikey', 'MirrorkeyController@json')->name('datatableapikey');
    Route::get('/admin/form/master-apikey-create', 'MirrorkeyController@create')->name('mirrorkey.create');
    Route::post('/admin/form/master-apikey-create', 'MirrorkeyController@store')->name('mirrorkey.store');
    Route::patch('/admin/form/master-apikey-update/{id}', 'MirrorkeyController@update')->name('mirrorkey.update');
    Route::get('/admin/form/master-apikey-edit/{id}', 'MirrorkeyController@edit')->name('mirrorkey.edit');
    Route::delete('/admin/form/master-apikey-delete', 'MirrorkeyController@destroy')->name('mirrorkey.destroy');

    Route::get('/admin/form-master-mirror', 'MasterMirrorController@json')->name('ApiMasterMirrorJson');
    Route::resource('cookies', 'GoogleDrivePlayerController');
});
