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

use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect', function () {
	$query = http_build_query([
		'client_id' => '5',
		'redirect_uri' => 'http://localhost:8090/callback',
		'response_type' => 'code',
		'scope' => '',
	]);
	
	return redirect('http://localhost:8080/oauth/authorize?'.$query);
});

Route::get('/callback', function (Request $request) {
	try {
		$http = new GuzzleHttp\Client;
		
		$response = $http->post('http://localhost:8080/oauth/token', [
			'form_params' => [
				'grant_type' => 'authorization_code',
				'client_id' => env('PERSONAL_CLIENT_ID'),
				'client_secret' => env('PERSONAL_CLIENT_SECRET'),
				'redirect_uri' => 'http://localhost:8090/callback',
				'code' => $request->code,
			],
		]);
		
		$resp = json_decode((string)$response->getBody(), true);
		
		return view('welcome')->with('response', $response->getBody());
	} catch (Exception $ex) {
		return Redirect::to('/');
	}
});

Route::get('/getUser', function (Request $request) {
	$http = new GuzzleHttp\Client;

	$response = $http->request('GET', 'http://localhost:8080/api/user', [
		'headers' => [
			'Accept' => 'application/json',
			'Authorization' => 'Bearer '.$request->access_token,
		],
	]);
	
	return json_decode((string)$response->getBody(), true);
});

Route::get('/logout', function (Request $request) {
	$http = new GuzzleHttp\Client;
	
	$response = $http->request('POST', 'http://localhost:8080/api/logout', [
		'headers' => [
			'Accept' => 'application/json',
			'Authorization' => 'Bearer '.$request->access_token,
		],
	]);
	
	return json_decode((string)$response->getBody(), true);
});