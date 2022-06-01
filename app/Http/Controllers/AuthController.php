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
            
            $validatedData = $request->validate([ 
                'password' => 'required',
                'newPassword' => 'required|string'
            ]);
            
            //Change Password
            User::find($request->userId)->update(['password' => Hash::make($request->newPassword)]);
            return response()->json(['status' => 200, 'success' => true, 'message' => "Hasło zostało zaktualizowane!"]);
        }else {
            return response()->json(['status' => 'failed', 'success' => false, 'message' => "Błąd!"]);
        }
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ]);

        $user = User::find($request->userId);

        if($request->file('image'))
        {
            if($this->guard()->user()->image != NULL)
            {
                Storage::disk('user_avatars')->delete($this->guard()->image);
            }

            $avatar_name = $this->random_char_gen(20).'.'.$request->file('image')->getClientOriginalExtension();
            $avatar_path = $request->file('image')->storeAs('', $avatar_name, 'user_avatars');

            $profile = User::find($request->user()->id);
            $profile->avatar = $avatar_path;

            if($profile->save())
            {
                return response()->json(['status' => 200, 'message' => 'Avatar został zaktualizowany!', 'image_url' => url('storage/user-avatar/'.$avatar_path), 'user' => $user]);
            }else {
                return response()->json(['status' => 'failed', 'message' => 'Wystąłpił błąd w przesyłaniu obrazu!', 'image_url' => NULL]);
            }
        }
        if($request->name)
        {
            $user->name = $request->name;
            $user->update();

            return response()->json(['status' => 200, 'success' => true, 'message' => 'Twoja nazwa użytkownika została zaktualizowana!', 'user' => $user]);
        }
        if($request->email)
        {
            $user->email = $request->email;
            $user->update();

            return response()->json(['status' => 200, 'success' => true, 'message' => 'Twój e-mail został zaktualizowany!', 'user' => $user]);
        }
        if($request->phone)
        {
            $user->phone = $request->phone;
            $user->update();
            
            return response()->json(['status' => 200, 'success' => true, 'message' => 'Twój numer telefonu został zaktualizowany', 'user' => $user]);
        }
        // return response()->json(['status' => 'failed', 'message' => 'nie wybrano żadnego pliku!', 'image_url' => NULL]);
        
        return response()->json(['status' => 'failed', 'success' => false, 'message' => 'Formularz jest pusty. Nie zaktualizowano żadnych wartości']);
    }
}
