<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessExampleJob;
use Illuminate\Http\Request;
use App\Models\User;
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

}
