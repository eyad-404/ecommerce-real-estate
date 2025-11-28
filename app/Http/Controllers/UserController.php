<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\property;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();
            return view('users.index', compact('users'));
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('users.create');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    
    public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:30',
        'email' => 'required|string|email|unique:users,email|max:60',
        'password' => 'required|string|min:8',
        'birth_date' => 'required|date',
        'gender' => 'required|in:male,female,other',
        'location' => 'required|string|max:255',
        'phone' => 'required|numeric|digits_between:10,11',
        'role' => 'required|string|in:admin,seller,buyer'
    ]);
      
    $secretKey = env('PASSWORD_HMAC_KEY');
    $hmacHash = hash_hmac('sha256', $request->password, $secretKey);
    $bcryptHash = Hash::make($hmacHash);

    $user = User::create([
        'name' => $data['name'],
            'email'=> $data['email'],
            'password'=> $bcryptHash,
            'birth_date'=> $data['birth_date'],
            'gender'=> $data['gender'],
            'location'=> $data['location'],
            'phone'=> $data['phone'],
            'role'=> $data['role'],
    ]);

    if ($request->ajax()) {
        return response()->json(['success' => true, 'user' => $user]);
    }

    return back()->with('success', 'User created successfully');
}


    // UserController.php
public function search(Request $request)
{
    $query = $request->input('query');

    $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->get();

    // Return JSON if AJAX
    if ($request->ajax()) {
        return response()->json($users);
    }

    // Otherwise, just return the same view with filtered users
    return view('users-management', ['users' => $users]);
}



    

    public function update(Request $request, User $user)
{
    try {
        $data = $request->validate([
            'name' => 'required|string|max:30',
                'email' => 'required|string|email|unique:users,email,' . $user->id . '|max:60',
                'password' => 'nullable|string|min:8',
                'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
                'location' => 'required|string|max:255',
                'phone' => 'required|numeric|digits_between:10,11',
            'role' => 'required|string|in:admin,seller,buyer',
        ]);

        if (!empty($data['password'])) {
            $secretKey = env('PASSWORD_HMAC_KEY');
            $hmacHash = hash_hmac('sha256', $data['password'], $secretKey);
            $bcryptHash = Hash::make($hmacHash);
    $data['password'] = $bcryptHash;
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Return JSON for AJAX
        return response()->json(['success' => true, 'user' => $user]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}



    public function destroy(User $user)
{
    try {
        $user->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    } catch (\Exception $e) {
        if(request()->ajax()) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

    public function usersManagement()
    {
        $users = User::all();
        $properties = Property::all();
        return view('users.users-management', compact('users', 'properties'));
    }

}
