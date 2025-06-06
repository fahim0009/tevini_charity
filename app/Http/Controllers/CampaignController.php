<?php

namespace App\Http\Controllers;

use App\Mail\CampaignReport;
use App\Models\Charity;
use App\Models\Campaign;
use App\Models\ContactMail;
use App\Models\Usertransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PDF;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $data = Campaign::orderBy('id', 'DESC')
                ->when(request()->input('campaign'), function ($query, $campaign) {
                    return $query->where('id', $campaign);
                })
                ->when(request()->input('charity'), function ($query, $charity) {
                    return $query->where('charity_id', $charity);
                })
                ->get();



        return view('campaign.index', compact('data'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'charity_id' => 'required|integer|exists:charities,id',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

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

        if(empty($request->end_date)){
            $message ="Please fill campaign end date";
            return back()->with('message', $message);
            exit();
        }

        $secret = time();
        $data = new Campaign;
        $data->campaign_title = $request->title;
        $data->charity_id = $request->charity_id;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
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
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;

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

    public function getAllCampaignDonor($id = Null)
    {
        if ($id) {
            $data = Usertransaction::select('id', 'user_id','amount','campaign_id','created_at')->whereNotNull('campaign_id')->orderBy('id','DESC')->where('campaign_id', $id)->get();
        } else {
            $data = Usertransaction::select('id', 'user_id','amount','campaign_id','created_at')->whereNotNull('campaign_id')->orderBy('id','DESC')->get();
        }

        
        // dd($data);
        return view('campaign.donorlist', compact('data', 'id'));
    }


    public function campaignReport(Request $request)
    {
        $charityEmail = $request->email;

        $request->validate([
            'email' => 'required|email',
        ]);


            $reportid = rand(1000, 9999);
            $data = Usertransaction::select('id', 'user_id','amount','campaign_id','created_at')->whereNotNull('campaign_id')->orderBy('id','DESC')->where('campaign_id', $request->campaignid)->get();

            $charity = Charity::find($request->charityid);
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['cc'] = $contactmail;

            $pdf = PDF::loadView('invoices.campaign_report', compact('data','charity'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'Report#'.$reportid.'.pdf', $output);
            
            $array['name'] = $charity->name;
            $array['view'] = 'mail.campaignreport';
            $array['subject'] = 'Campaign report';
            $array['from'] = 'info@tevini.co.uk';
            $array['content'] = 'Hi, Your campaign report has been placed';
            $array['file'] = public_path().'/invoices/Report#'.$reportid.'.pdf';
            $array['file_name'] = 'Report#'.$reportid.'.pdf';
            $array['subjectsingle'] = 'Report Placed - '.$reportid;

            Mail::to($charity->email)->cc($contactmail)->send(new CampaignReport($array));

            $message = "Campaign report sent successfully";
            return back()->with('message', $message);


    }


}
