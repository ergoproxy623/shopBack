<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\User;

class UserController extends Controller
{
    //list all users with them purchase
    public function index()
    {
        $user = User::where('id','!=',auth()->user()->id)
            ->with('purchase')
            ->get();
        return response()->json(['success' => true, 'data' => $user]);
    }

    // all user's data
    public function show(User $user)
    {
        return response()->json(['success' => true, 'data' => $user]);
    }

    // list all videos all users, what they bought
    public function usersWhoBought()
    {
        $user= User::where('status', '!=', 0)
            ->with('purchase')
            ->get();
        return response()->json(['success' => true, 'data' => $user]);
    }
}
