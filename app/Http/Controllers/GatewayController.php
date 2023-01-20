<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use Illuminate\Http\Request;
use Auth;
use DB;

class GatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $gateways = Gateway::all();
        return view('gateway.index')->with('gateways',$gateways);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(empty($request->gatewayname)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Gateway Name field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->gatewayname)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Return URL field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $gate = new Gateway;
        $gate->gateway_name = $request->gatewayname;
        $gate->return_url = $request->returnurl;
        $gate->status = "1";
        $gate->created_by = Auth::user()->name;
        if($gate->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Gateway Added Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

        return response()->json(['status'=> 303,'message'=>'Server Error!!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gateway  $gateway
     * @return \Illuminate\Http\Response
     */
    public function show(Gateway $gateway)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gateway  $gateway
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info = Gateway::where('id','=', $id)->first();
        return response()->json($info);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gateway  $gateway
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $gate = Gateway::findOrFail($id);
        $gate->gateway_name = $request->gatewayname;
        $gate->return_url = $request->returnurl;
        $gate->updated_by = Auth::user()->name;
        if($gate->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Gateway Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gateway  $gateway
     * @return \Illuminate\Http\Response
     */
    public function destroy($gateway)
    {
        if(Gateway::destroy($gateway)){
            return response()->json(['success'=>true,'message'=>'Gateway has been deleted successfully']);
        }else{
            return response()->json(['success'=>$gateway,'message'=>'Delete Failed']);
        }

    }
}
