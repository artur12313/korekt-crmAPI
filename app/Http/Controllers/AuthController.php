<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
{
    /**Validate the data using validation rules
    */
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
    ]);
         
    /**Check the validation becomes fails or not
    */
    if ($validator->fails()) {
        /**Return error message
        */
        return response()->json([ 'status' => 'failed', 'message' => 'validation_error', 'errors' => $validator->errors()]);
    }

    /**Store all values of the fields
    */
    $newuser = $request->all();

        /**Create an encrypted password using the hash
    */
    $newuser['password'] = Hash::make($newuser['password']);

    $user_status = User::where('email', $request->email)->first();
    if(!is_null($user_status)){
        return response()->json(['status' => 'failed', 'success' => false, 'message' => "Ups! Ten email jest już zajęty"]);
    }
    /**Insert a new user in the table
    */
    $user = User::create($newuser);

        /**Create an access token for the user
    */
    $success['token'] = $user->createToken('AppName')->accessToken;
    /**Return success message with token value
    */
    if(!is_null($user)){
        return response()->json(['status' => 200, 'success' => true, 'message' => 'Twoje konto zostało utworzone pomyślnie', 'data' => $user]);
    } else {
        return response()->json(['status' => 'failed', 'success' => false, 'message' => 'Błąd!']);
    }
    
}

public function login(Request $request)
{
    /**Read the credentials passed by the user
    */
    $credentials = [
        'email' => $request->email,
        'password' => $request->password
    ];

    /**Check the credentials are valid or not
    */
    if( auth()->attempt($credentials) ){
        /**Store the information of authenticated user
        */
        $user = Auth::user();
        /**Create token for the authenticated user
        */
        $success['token'] = $user->createToken('AppName')->accessToken;
        return response()->json(['status' => 200, 'success' => true, 'message' => 'Pomyślnie zalogowano', 'data' => $user]);
    } else {
        /**Return error message
        */
        return response()->json(['status' => 'failed', 'success' => false, 'message' => 'Błędne dane logowania. Spróbuj ponownie']);
    }
}

public function userDetail($email) {
    $user = array();
    if($email != ''){
        $user = User::where('email', $email)->first();
        return $user;
    }
}

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
