<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->hobbies = $request->hobbies;
        $user->address = $request->address;
        $user->address = $request->address;
        $user->profile = $request->file('profile')->store('products');
        $user->save();

        return response()->json(['message' => 'Registration successful'], 201);
    }
    public function view()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User data deleted successfully'], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:users,name,' . $id,
            'email' => 'string|email|max:255|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->hobbies = $request->hobbies;
        $user->address = $request->address;
        if ($request->hasFile('profile')) {
            $storage = Storage::disk('products');
            if ($storage->exists($user->profile)) {
                $storage->delete($user->profile);
            }
            $path = $request->file('profile')->store('profiles', 'products');
            $user->profile = $path;
            $user->save();
        }
        
        $user->save();

        return response()->json(['message' => 'User data updated successfully'], 200);
    }

    public function data(Request $request, $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);

    }

    
}




