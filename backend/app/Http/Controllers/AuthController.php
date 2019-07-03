<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','signup','changePassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // $request->password = bcrypt($request->password);
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'User Does Not Exist'], 401);
        }

        return $this->respondWithToken($token);
    }


    /**
     *  sign up and Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(Request $request)
    {

        $validator=Validator::make($request->all(), [
            'name' => 'required',
            'email'=>'required|unique:users',

            'password'=> 'required|confirmed'
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->messages()],401);
        }
        $input=$request->all();
        $input['password']=bcrypt($input['password']);

        $user= User::create($input);
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => ['error'=>'User Does Not Exist']], 401);
        }

        return $this->respondWithToken($token);
    }



    /**
     *  sign up and Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {

        $validator=Validator::make($request->all(), [

            'email'=>'required',
            'resetToken'=>'required',
            'password'=> 'required|confirmed'
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->messages()],401);
        }

        $email=$request->email;
        $resetToken=$request->resetToken;
        $resetcheck=DB::table('password_resets')->where('email',$email)->where('token',$resetToken)->first();
        if ($resetcheck) {
            $user=User::where('email',$email)->first();
            if ($user) {
        $input=$request->all();
        $input['password']=bcrypt($input['password']);

        $user->password=$input['password'];
        $user->save();
        DB::table('password_resets')->where('email',$email)->where('token',$resetToken)->delete();
        // $credentials = request(['email', 'password']);

        // if (!$token = auth()->attempt($credentials)) {
        //     return response()->json(['error' =>  ['error'=>'User Does Not Exist']], 401);
        // }

        // return $this->respondWithToken($token);
        return response()->json(['success' =>  'Password changed succefully'], 200);
            }else{
                return response()->json(['error' =>  ['error'=>'User Does Not Exist']], 401);
            }
        }else{
            return response()->json(['error' =>  ['error'=>'Invalid TOken or Email']], 401);
        }




    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
        //return auth()->user();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()->name,
        ]);
    }




}
