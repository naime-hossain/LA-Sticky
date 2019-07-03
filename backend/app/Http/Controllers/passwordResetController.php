<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use App\Mail\resetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class passwordResetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request)
    {

    $email=$request->email;
     if ($this->validateEmail($email)) {
         $token=$this->createToken($email);
         $this->send($email,$token);

         return $this->successResponse();

     }





     return $this->failedResponse();



    }




    public function createToken($email){
       $oldtoken= DB::table('password_resets')->where('email',$email)->first();
        if($oldtoken){
            return $oldtoken->token;
        }
        $token=str_random(60);
       $this->saveToken($token,$email);
       return $token;

    }
    public function saveToken($token,$email){
     DB::table('password_resets')->insert([
            'email'=>$email,
            'token'=>$token,
            'created_at'=>Carbon::now()
        ]);
    }

public function send($email,$token){
 Mail::to($email)->send(new resetPasswordMail($token));

 }
    public function failedResponse(){
        return response()->json([
          'error'=>'Email does not found in our databse'
         ],404);
    }
   public function successResponse(){
        return response()->json([
          'data'=>'password reset link send.please check email'
         ],200);
    }
 public function validateEmail($email)
    {
     return $isUSer=!!User::where('email',$email)->first();
    }

}
