<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister(){
        return view('myauth.register');
    }
    public function register(Request $request){
    try {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8|confirmed',
            'phone'=>'required|digits_between:10,11',
            'role'=>'required|in:admin,developer,buyer,seller',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'location' => 'required|string|max:255',
        ]);

        $secretKey = env('PASSWORD_HMAC_KEY');
        $hmacHash = hash_hmac('sha256', $data['password'], $secretKey);
        $bcryptHash = Hash::make($hmacHash);
        

        $user = User::create([
            'name' => $data['name'],
            'email'=>$data['email'],
            'password'=>$bcryptHash,
            'phone'=>$data['phone'],
            'role'=>$data['role'],
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'location' => $data['location'],
        ]);

        Auth::login($user);
        return redirect()->route('home')->with('success',"account created successfully");
    } catch (\Exception $e) {
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
    }
    public function showlogin(){
        return view('myauth.login');
    }
    public function login(Request $request){
      try {
        $credentials =$request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $secretKey = env('PASSWORD_HMAC_KEY');
        $hmacHash = hash_hmac('sha256', $request->password, $secretKey);


        $user = User::where('email', $request->email)->first();

            if($user && Hash::check($hmacHash, $user->password)){
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('home')->with('success','Login success');
            }
        // if(Auth::attempt($credentials)){
        //     $request->session()->regenerate();
        //     return redirect()->route('home')->with('success','login success');
        // }
        return back()->withErrors(['email'=>'invalid credentials']);
        } catch (\Exception $e) {
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function checkEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $exists = \App\Models\User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
}

}
