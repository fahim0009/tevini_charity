<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Usertransaction;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $data = Campaign::orderBy('id','DESC')->get();
        return view('campaign.index', compact('data'));
    }


    public function store(Request $request)
    {


        if(empty($request->charity_id)){
            $message ="Please select charity";
            return back()->with('message', $message);
            exit();
        }

        if(empty($request->title)){
            $message ="Please fill campaign title";
            return back()->with('message', $message);
            exit();
        }
        $secret = time();
        $data = new Campaign;
        $data->campaign_title = $request->title;
        $data->charity_id = $request->charity_id;
        $data->status = "1";
        $data->hash_code = hash_hmac("sha256", $secret, $request->title);

        if($data->save()){

            $message ="Campaign Created Successfully";
            return back()->with('message', $message);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }

    public function edit($id)
    {

        $data = Campaign::where('id','=' ,decrypt($id))->first();
        return view('campaign.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = Campaign::findOrFail($id);
        $data->charity_id = $request->charity_id;
        $data->campaign_title = $request->title;

        if($data->save()){
            $message ="Campaign Update Successfully";

        return redirect()->route('campaign')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }

    public function delete(Request $request)
    {
        if( Campaign::destroy($request->campaignId)){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Campaign deleted successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Delete Fail...</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
    }


    public function updateUrl(Request $request)
    {

        if(empty($request->campaignurl)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill url field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $cmpn = Campaign::find($request->campaignid);
        $cmpn->return_url = $request->campaignurl;
        if($cmpn->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Overdrawn amount update successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }

    public function getAllCampaignDonor()
    {
        $data = Usertransaction::select('id', 'user_id','amount','created_at')->whereNotNull('campaign_id')->orderBy('id','DESC')->get();
        // dd($data);
        return view('campaign.donorlist', compact('data'));
    }


}
