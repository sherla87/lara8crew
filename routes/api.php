<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/hello', function () {
   return( 'hello');
});

Route::get('/init', function () {
   Schema::dropIfExists('crew');
   if (!Schema::hasTable('crew')) {
       Schema::create('crew', function (Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->string('email')->unique();
       });
   }
  
   $json_data = '{"table":"crew","status":"init"}';


   $result = json_decode($json_data);


   return response()->json($result, 201);
});

Route::get('/show_tables',function(){
   //$tables =  DB::select('SHOW TABLES');
   $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
   echo "tables:<br/>";
   foreach($tables as $table){
       $arry =  (array) $table;
       foreach ($arry as $value) {
           echo $value."<br/>";
       }
   }
});


Route::match(array('GET', 'POST'),'/getall', function () {
   $student = DB::select('select * from crew');
   return response()->json($student, 200);
});


Route::get('/insert/{name}/{email}',function($name,$email){
   $timestamp = time();
   DB::insert('insert into crew (id, name,email)
       values (?, ?,?)',
       [$timestamp,$name.'_'.$timestamp,$email.'_'.$timestamp]);
   echo "record inserted.<br/>";
});


Route::post('/insert',function(Request $request)
 {
   $payload = json_decode($request->getContent(), true);
   try {
     // Get data here, eg. make an external API request or DB query
     $response = [
       'name' => $payload['name'],
       'email' => $payload['email']
     ];


   $timestamp = time();
   DB::insert('insert into crew (id, name,email)
       values (?, ?,?)',
       [$timestamp,$response['name'].'_'.$timestamp
       ,$response['email'].'_'.$timestamp]);




   } catch (\GuzzleHttp\Exception\BadResponseException $e) {
     $errorResJson = $e
       ->getResponse()
       ->getBody()
       ->getContents();
     $errorRes = json_decode(stripslashes($errorResJson), true);
     // Return error
     return response()->json(
       [
         'message' => 'error',
         'data' => '$errorRes'
       ],
       $errorRes['response']['code']
     );
   }
   // Return success
   return response()->json(
     [
       'status' => '200',
       'data' => $response,
       'message' => 'success'
     ],
     200
   );
 }
);

Route::post('/update',function(Request $request)
 {
  $payload = json_decode($request->getContent(), true);
   try {
     $response = [
       'id' => $payload['id'],
       'name' => $payload['name'],
       'email' => $payload['email']
     ];


   $affected = DB::update(
   'update crew set name = ?, email=? where id = ?',
   [$response['name'],$response['email'],$response['id']]);




   } catch (\GuzzleHttp\Exception\BadResponseException $e) {
     $errorResJson = $e
       ->getResponse()
       ->getBody()
       ->getContents();
     $errorRes = json_decode(stripslashes($errorResJson), true);
     // Return error
     return response()->json(
       [
         'message' => 'error',
         'data' => '$errorRes'
       ],
       $errorRes['response']['code']
     );
   }
   // Return success
   return response()->json(
     [
       'status' => '200',
       'data' => $affected ,
       'message' => 'success'
     ],
     200
   );
 }
);



Route::post('/delete',function(Request $request)
 {
  $payload = json_decode($request->getContent(), true);
   try {
     $response = [
       'id' => $payload['id']
     ];


   $deleted = DB::delete('delete from crew where id = ?',
   [$response['id']]);




   } catch (\GuzzleHttp\Exception\BadResponseException $e) {
     $errorResJson = $e
       ->getResponse()
       ->getBody()
       ->getContents();
     $errorRes = json_decode(stripslashes($errorResJson), true);
     // Return error
     return response()->json(
       [
         'message' => 'error',
         'data' => '$errorRes'
       ],
       $errorRes['response']['code']
     );
   }
   // Return success
   return response()->json(
     [
       'status' => '200',
       'data' => $deleted ,
       'message' => 'success'
     ],
     200
   );
 }
);