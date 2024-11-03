<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessExampleJob;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $data = User::all();
        ProcessExampleJob::dispatch($data);

    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function store(Request $request)
    {
        $user = User::create($request->all());
        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function update(Request $request)
    {
        $user = Auth::user()->id;
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        if($request->password){
           $password = Hash::make($request->password);
           $request->merge(['password' => $password]);
        }
        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully'], 200);
    }
}
