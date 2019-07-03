<?php

namespace App\Http\Controllers;

use App\sticky;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class stickyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      if (Auth::check()) {
         $user=Auth::user();
         $stickies=$user->stickies()->orderBy('id','desc')->get();
         return response()->json(['data'=>$stickies],200);

      }

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user=Auth::user();
        //return $user;
            $validator=Validator::make($request->all(), [

            'title'=>'required',
            'description'=>'required',
            'color'=> 'required',
            // 'user_id'=> 'required',
        ]);
       // return $request->all();

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->messages()],401);
        }

        //create sticky
        $sticky=$user->stickies()->create($request->all());

        if($sticky) {
            return response()->json(['data'=>$sticky],200);
        }else{
            return response()->json(['error'=>['error'=>'something went wrong']],400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $sticky=sticky::find($id);
       $sticky->is_done=1;
       $sticky->update();
       return response()->json(['data'=>['success'=>'Note is Completed']],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
