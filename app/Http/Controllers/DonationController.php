<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonationCalculator;
use App\Models\DonationDetail;
use App\Models\OtherDonation;
use App\Models\Usertransaction;
use Carbon\Carbon;
use Auth;

class DonationController extends Controller
{
    public function store(Request $request)
    {
        $start_dates = $request->start_dates;
        $income_amounts = $request->income_amounts;
        $income_titles = $request->income_titles;
        $income_slots = $request->income_slots;
        $donation_percentages = $request->donation_percentages;

        foreach($income_amounts as $key => $income_amount){
            if($start_dates[$key] == "" || $income_amount == "" || $income_titles[$key] == "" || $income_slots[$key] == "" || $donation_percentages[$key] == ""){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill all field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
            }
        }


        foreach($income_amounts as $key => $income_amount)
        {
            $data = new DonationCalculator;
            $data->start_date = $start_dates[$key];
            $data->income_amount = $income_amount;
            $data->income_title = $income_titles[$key];
            $data->income_slot = $income_slots[$key];
            $data->donation_percentage = $donation_percentages[$key];
            $data->status = 1;
            $data->donor_id = Auth::user()->id;
            $data->save();

        }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data created successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);

    }




    public function oneoffDonation(Request $request)
    {
        $ostart_date = $request->ostart_date;
        $oincome_amount = $request->oincome_amount;
        $oincome_title = $request->oincome_title;
        $odonation_percentage = $request->odonation_percentage;

        if(empty($ostart_date)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Date field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($oincome_amount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Amount field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($oincome_title)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Title field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        if(empty($odonation_percentage)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please Choose Your Percentage.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


            $data = new DonationDetail;
            $data->date = $ostart_date;
            $data->income_amount = $oincome_amount;
            $data->income_title = $oincome_title;
            $data->income_slot = 0;
            $data->donation_percentage = $odonation_percentage;
            $data->donation_amount = $oincome_amount * ($odonation_percentage/100);;
            $data->status = 1;
            $data->donor_id = Auth::user()->id;
            $data->save();


        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>One-off income added successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);

    }



    public function DcalUpdate(Request $request)
    {

        $start_dates = $request->start_dates;
        $donorcal_ids = $request->donorcal_ids;
        $income_amounts = $request->income_amounts;
        $income_titles = $request->income_titles;
        $income_slots = $request->income_slots;
        $donation_percentages = $request->donation_percentages;

        foreach($income_amounts as $key => $income_amount){
            if($start_dates[$key] == "" || $income_amount == "" || $income_titles[$key] == "" || $income_slots[$key] == "" || $donation_percentages[$key] == ""){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill all field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
            }
        }


        foreach($income_amounts as $key => $income_amount)
        {
            if(isset($donorcal_ids[$key])){

            $data =  DonationCalculator::findOrFail($donorcal_ids[$key]);
            $data->start_date = $start_dates[$key];
            $data->income_amount = $income_amount;
            $data->income_title = $income_titles[$key];
            $data->income_slot = $income_slots[$key];
            $data->donation_percentage = $donation_percentages[$key];
            $data->donor_id = Auth::user()->id;
            $data->save();

            }else{

            $data = new DonationCalculator;
            $data->start_date = $start_dates[$key];
            $data->income_amount = $income_amount;
            $data->income_title = $income_titles[$key];
            $data->income_slot = $income_slots[$key];
            $data->donation_percentage = $donation_percentages[$key];
            $data->status = 1;
            $data->donor_id = Auth::user()->id;
            $data->save();

            }

        }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data created successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);


    }

    public function donationCal(Request $request)
    {
        // donation calculaton start
        $dt = Carbon::now();
        $sub = $dt->month - 4; // sub month will change in every month

        $first_date = DonationDetail::where('donor_id', Auth::user()->id)->orderBy('date','ASC')->first();

        if(empty($first_date->date)){$start_date=now();}else{$start_date=$first_date->date;}

        // dd($first_date);
        // $tevini_donation = Usertransaction::where('user_id', Auth::user()->id)
        //                             ->where('t_type','=','Out')
        //                             ->whereBetween('created_at',
        //                                 [Carbon::now()->subMonth($sub), Carbon::now()]
        //                             )
        //                             ->sum('amount');

        $tevini_donation = Usertransaction::where([
            ['user_id','=', Auth::user()->id],
            ['t_type','=', 'Out'],
            ['status','=', '1']
        ])->whereDate('created_at','>=',$start_date)->sum('amount');



        // $donationamnt = DonationDetail::where('donor_id', Auth::user()->id)
        //             ->whereBetween('date',
        //                 [Carbon::now()->subMonth($sub), Carbon::now()]
        //             )
        //             ->sum('donation_amount');

        $donationamnt = DonationDetail::where('donor_id', Auth::user()->id)->sum('donation_amount');


        // $otherdonation = OtherDonation::where('donor_id', Auth::user()->id)
        //                         ->whereBetween('donation_date',
        //                             [Carbon::now()->subMonth($sub), Carbon::now()]
        //                         )
        //                         ->sum('d_amount');

        $otherdonation = OtherDonation::where('donor_id', Auth::user()->id)->whereDate('donation_date','>=',$first_date)->sum('d_amount');


        if($donationamnt){
            $availabledonation =$donationamnt - ($tevini_donation + $otherdonation);

        }else{
            $availabledonation = 0;
        }


        $donor_cals = DonationCalculator::where('donor_id', Auth::user()->id)->where('status','=','1')->get();

        if($donor_cals->isEmpty()){

            $msg = "Fill this form for donation calculation";
            return view('frontend.user.donationcal',compact('tevini_donation','otherdonation','availabledonation','donor_cals','msg'));


        }else{

        foreach($donor_cals as $donor_cal)
        {

        $donationdetails = DonationDetail::where('donation_cal_id', $donor_cal->id)->orderBy('id', 'desc')->first();

            if (isset($donationdetails) && ($dt->toDateString() > $donationdetails->date)) {
                $last_date = Carbon::parse($donationdetails->date);
                $diff_with_lastdate = $last_date->diffInDays($dt);

                if($donor_cal->income_slot != "0"){

                        if($diff_with_lastdate >= $donor_cal->income_slot){
                        for($x=$donor_cal->income_slot; $x <= $diff_with_lastdate; $x+=$donor_cal->income_slot){
                            $doncaldetl = new DonationDetail;
                            $doncaldetl->donor_id = Auth::user()->id;
                            $doncaldetl->donation_cal_id = $donor_cal->id;
                            $doncaldetl->date = $last_date->addDays($donor_cal->income_slot);
                            $doncaldetl->income_amount = $donor_cal->income_amount;
                            $doncaldetl->income_slot = $donor_cal->income_slot;
                            $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                            // $doncaldetl->available_for_donation = $totaltran;
                            $doncaldetl->save();
                            $donationdetails = DonationDetail::where('donation_cal_id', $donor_cal->id)->orderBy('id', 'desc')->first();
                            $last_date = Carbon::parse($donationdetails->date);
                        }
                    }
                }


            }elseif((!isset($donationdetails) && $dt->toDateString() > $donor_cal->start_date)) {

                $start_date = Carbon::parse($donor_cal->start_date);
                $diff_with_startdate = $start_date->diffInDays($dt);

                if($donor_cal->income_slot != "0"){

                if($diff_with_startdate >= $donor_cal->income_slot){

                for($x=$donor_cal->income_slot; $x < $diff_with_startdate; $x+=$donor_cal->income_slot){
                    $doncaldetl = new DonationDetail;
                    $doncaldetl->donor_id = Auth::user()->id;
                    $doncaldetl->donation_cal_id = $donor_cal->id;
                    $doncaldetl->date = $start_date->addDays($donor_cal->income_slot);
                    $doncaldetl->income_amount = $donor_cal->income_amount;
                    $doncaldetl->income_slot = $donor_cal->income_slot;
                    $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                    // $doncaldetl->available_for_donation = $totaltran;
                    $doncaldetl->save();
                    $donationdetails = DonationDetail::where('donation_cal_id', $donor_cal->id)->orderBy('id', 'desc')->first();
                    $start_date = Carbon::parse($donationdetails->date);
                    }

                }

                }
            }

        }


        }

        $donor_cals = DonationCalculator::where([
            ['donor_id','=', Auth::user()->id],
            ['income_slot','!=', '0']
        ])->get();
        return view('frontend.user.donationcal',compact('donor_cals','tevini_donation','otherdonation','availabledonation'));


    }


    public function otherDonationStore(Request $request)
    {

        if(empty($request->d_amount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Amount field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->donation_date)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Date field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->d_title)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Title field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = new OtherDonation;
        $data->d_title = $request->d_title;
        $data->donation_date = $request->donation_date;
        $data->d_amount = $request->d_amount;
        $data->donor_id = Auth::user()->id;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data created successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }

    public function donationDetails($id)
    {
        $donation = DonationDetail::where('donation_cal_id','=', $id)->orderBy('id','DESC')->get();
        return view('frontend.user.donationdetails',compact('donation'));
    }

    public function onOffdonationDetails()
    {
        $donation = DonationDetail::where([
            ['donor_id','=', Auth::user()->id],
            ['income_slot','=', '0']
        ])->orderBy('id','DESC')->get();
        return view('frontend.user.onnoffdonationdetails',compact('donation'));
    }

    public function donationActive(Request $request)
    {
        $data = DonationCalculator::find($request->id);
        $data->status = $request->status;
        $data->save();

        if($request->status==1){
            $data = DonationCalculator::find($request->id);
            $data->status = $request->status;
            $data->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Active Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $data = DonationCalculator::find($request->id);
            $data->status = $request->status;
            $data->save();
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Inactive Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }





}
