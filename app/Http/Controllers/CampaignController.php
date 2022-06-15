<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
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
        // $this->validate($request,[
        //     'title' => 'required',
        //     'fname' => 'required',
        // ]);

        $data = new Campaign;
        $data->campaign_title = $request->title;
        $data->charity_id = $request->charity_id;

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


}
