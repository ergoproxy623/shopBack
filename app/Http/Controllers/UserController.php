<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\UserRequest;
use App\Models\ContactUs;
use App\Models\UserVideo;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use JWTAuth;
use File;
use Config;
use Hash;
use DB;

class UserController extends Controller
{
    // User update him profile
    public function update(UserRequest $request)
    {
        $user = auth()->user();
        $oldPath = $user->avatar_path;
        $user->update($request->all());
        $savePath = \Illuminate\Support\Facades\Config::get('constants.image_folder.avatars.save_path');

        $file = $request->file('avatar_url');
        if($file) {
            if($oldPath){
                if (Storage::exists($oldPath)) {
                    Storage::delete($oldPath);
                }
            }
            $filename ='image'.rand(0000000,999999).'.'. $file->extension();
            $file->storeAs($savePath, $filename);
            $user->update(['avatar_url'=> $filename]);
        }
        return response()->json(['success' => true, 'data' => $user]);
    }

    // Get user's data via token
    public function updatePage()
    {
         return response()->json(JWTAuth::user());
    }

    // validation of "email" and "phone" fields
    public function forLogin(Request $request)
    {
        if($user = DB::table('users')->where('email',$request->email)->first())
            return response()->json(['success' =>  false,'error' =>  trans('message.email')], 200);
        if($user = DB::table('users')->where('telephone',$request->telephone)->first())
            if($user->telephone != null)
            return response()->json(['success' =>  false,'error' =>  trans('message.telephone')], 200);
        return response()->json(['success' =>  true]);
    }

    //Update user's password
    function updatePassword(PasswordRequest $request)
    {
        $user = JWTAuth::user();
        if(Hash::check($request->get('password'), $user->password)) {
            $user->update(['password' => Hash::make($request->new_password)]);
            return response()->json(['success' => true, 'message' => trans('passwords.update')]);
        }
        return response()->json(['success' => false, 'error' => trans('passwords.error')], 400);
    }

    //Reset user's password
    public function reset(Request $request, User $user)
    {
        $email = User::where('email', $request->email)->first();
        if (!$email) {
            return response()->json(['success' => false, 'error' => trans('passwords.user')]);
        }
        $pass = str_random(8);
        $email->password = Hash::make($pass);
        $email->update($request->only('password'));
        Mail::send('password', compact('pass'), function ($message) use ($request) {
            $message->to($request->email)->subject('New Password');
        });
        return response()->json(['success' => true, 'message' => trans('passwords.reset')]);
    }

    // Send feedback to all administrators
    public function Feedback(FeedbackRequest $request)
    {
        $email = User::where('role_id', 2)->pluck('email');
        if (!$email) {
            return response()->json(['success' => false, 'error' => 'Email dont exist!']);
        }
        $data = ContactUs::create($request->all());

        Mail::send('userData', compact('data'), function ($message) use ($email) {
            foreach ($email as $value) {
                $message->to($value)->subject('User message');
            }
        });
        return response()->json(['success' => true, 'message' => 'Email has been sent']);
    }
}
