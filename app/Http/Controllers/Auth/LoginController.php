<?php

namespace App\Http\Controllers\Auth;
use App\Http\Requests\SocialLoginUserRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use JWTAuth;
use App\Models\User;
use App\Http\Resources\UserResource;
use Tymon\JWTAuth\Exceptions\JWTException;
use DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    //authenticate user
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                if(!$user = DB::table('users')->where('email',$request->email)->first())
                    return response()->json(['error' =>  trans('auth.email')], 400);
                else
                    return response()->json(['error' =>  trans('auth.password')], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => trans('auth.token')], 500);
        }
        return response()->json(['success' => true, 'data' => ['user' => new UserResource(JWTAuth::user()), 'token' => $token]]);
    }

    //authenticate for admin panel
    public function loginAdmin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                if(!$user = DB::table('users')->where('email',$request->email)->first())
                return response()->json(['error' =>  trans('auth.email')], 400);
                else
                return response()->json(['error' =>  trans('auth.password')], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => trans('auth.token')], 500);
        }
        if (JWTAuth::user()->role_id == 2) {
            return response()->json(['success' => true, 'data' => ['user' => new UserResource(JWTAuth::user()), 'token' => $token]]);
        }
        else {
            return response()->json(['success' => false, 'error' => trans('message.access')]);
        }
    }

    //Login via Facebook
    public function socialLogin(SocialLoginUserRequest $request, User $users)
    {
        try {
            if ($user = User::where('social_id', $request->social_id)->first()) {
                $token = JWTAuth::fromUser($user);
            } else {
                $user = $users->createUser($request->all());
                $token = JWTAuth::fromUser($user);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => trans('auth.token')], 500);
        }
        return response()->json(['success' => true, 'token' => $token, 'data' =>$user]);
    }


}