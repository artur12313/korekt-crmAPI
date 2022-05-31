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
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
    ]);
         
    if ($validator->fails()) {
        return response()->json([ 'status' => 'failed', 'message' => 'validation_error', 'errors' => $validator->errors()]);
    }

    $newuser = $request->all();

    $newuser['password'] = Hash::make($newuser['password']);

    $user_status = User::where('email', $request->email)->first();
    if(!is_null($user_status)){
        return response()->json(['status' => 'failed', 'success' => false, 'message' => "Ups! Ten email jest już zajęty"]);
    }

    $user = User::create($newuser);

    $success['token'] = $user->createToken('AppName')->accessToken;
    
    if(!is_null($user)){
        return response()->json(['status' => 200, 'success' => true, 'message' => 'Twoje konto zostało utworzone pomyślnie', 'data' => $user]);
    } else {
        return response()->json(['status' => 'failed', 'success' => false, 'message' => 'Błąd!']);
    }
    
}

public function login(Request $request)
{
    $credentials = [
        'email' => $request->email,
        'password' => $request->password
    ];
    if( auth()->attempt($credentials) ){

        $user = Auth::user();

        $success['token'] = $user->createToken('AppName')->accessToken;
        return response()->json(['status' => 200, 'success' => true, 'message' => 'Pomyślnie zalogowano', 'data' => $user]);
    } else {
    
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

    public function updatePassword(Request $request){ 
        $credentials = [
            'password' => $request->password,
            'id' => $request->userId
        ];

        $user_status = User::where('id', $request->userId)->first();
        
        if($user_status){
            if (!(Hash::check($request->password, $user_status->password)))
            { 
                // The passwords matches
                return response()->json(['status' => 'failed', 'success' => false, 'message' => 'Twoje obecne hasło nie pasuje do hasła, które podałeś. Proszę spróbuj ponownie.']);
            }
            if(strcmp($request->password, $request->newPassword) == 0)
            {
                //Current password and new password are same
                return response()->json(['status' => 'failed', 'success' => false, 'message' => 'Nowe hasło nie może być takie samo jak obecne hasło. Wybierz inne hasło.']); 
            }
            
            // $validatedData = $request->validate([ 'currentPassword' => 'required', 'new-password' => 'required|string|min:6|confirmed', ]);
            // //Change Password
            // DB::table('users')->where('id', Auth::id())->update(['password' => Hash::make($request->get('new-password'))]);
            // return redirect()->back()->with("success","Hasło zostało pomyślnie zmienione !");
            // return response()->json(['status' => 200, 'success' => true, 'message' => "Hasło zostało zaktualizowane!"]);
        }else {
            return response()->json(['status' => 'failed', 'success' => false, 'message' => "Błąd!"]);
        }



        return response()->json(['status' => 200, 'success' => true, 'message' => 'Pomyślnie zalogowano']);
    }
}
