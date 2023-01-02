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


    public function update(Request $request)
    {

        if(empty($request->income_amount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Income Amount field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(!isset($request->income_slot)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Slot field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->donation_percentage)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Percent field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data =  DonationCalculator::findOrFail($request->dcalid);
        $data->income_amount = $request->income_amount;
        $data->income_slot = $request->income_slot;
        $data->donation_percentage = $request->donation_percentage;
        $data->donor_id = Auth::user()->id;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data created successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }

    public function donationCal(Request $request)
    {
        // donation calculaton start
        $dt = Carbon::now();
        $sub = $dt->month - 4; // sub month will change in every month
        $start_date = Carbon::now()->subMonth($sub); //start date - cut off date.
        $diff = $start_date->diffInDays($dt); // different days between current date & start date.


        $totaltran = Usertransaction::where('user_id', Auth::user()->id)
                                    ->where('t_type','=','Out')
                                    ->whereBetween('created_at',
                                        [Carbon::now()->subMonth($sub), Carbon::now()]
                                    )
                                    ->sum('amount');


        $donationamnt = DonationDetail::where('donor_id', Auth::user()->id)
                    ->whereBetween('date',
                        [Carbon::now()->subMonth($sub), Carbon::now()]
                    )
                    ->sum('donation_amount');

        $totalotherdonation = OtherDonation::where('donor_id', Auth::user()->id)
                                ->whereBetween('donation_date',
                                    [Carbon::now()->subMonth($sub), Carbon::now()]
                                )
                                ->sum('d_amount');

        if($donationamnt){
            $availabledonation = $totaltran - $donationamnt - $totalotherdonation;

        }else{
            $availabledonation = 0;
        }


        $donor_cals = DonationCalculator::where('donor_id', Auth::user()->id)->get();

        if(empty($donor_cals)){

            $msg = "Fill this form for donation calculation";
            $dondetails = DonationCalculator::with('donationdetail')->where('donor_id','=', Auth::user()->id)->get();
            return view('frontend.user.donationcal',compact('totaltran','totalotherdonation','availabledonation','dondetails','msg'));


        }else{

        $donationdetails = DonationDetail::where('donor_id', Auth::user()->id)->orderBy('id', 'DESC')->first();

            if ($donationdetails) {
                $last_date = Carbon::parse($donationdetails->date);
                $diff_with_lastdate = $last_date->diffInDays($dt);

                // dd($last_date);
                // if($donor_cal->income_slot != "0"){

                    //     if($diff_with_lastdate >= $donor_cal->income_slot){
                    //     for($x=$donor_cal->income_slot; $x <= $diff_with_lastdate; $x+=$donor_cal->income_slot){
                    //         $doncaldetl = new DonationDetail;
                    //         $doncaldetl->donor_id = Auth::user()->id;
                    //         $doncaldetl->donation_cal_id = $donor_cal->id;
                    //         $doncaldetl->date = $last_date->addDays($x);
                    //         $doncaldetl->income_amount = $donor_cal->income_amount;
                    //         $doncaldetl->income_slot = $donor_cal->income_slot;
                    //         $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                    //         $doncaldetl->available_for_donation = $totaltran;
                    //         $doncaldetl->save();
                    //     }
                    // }
                // }


            } else {

                // if($donor_cal->income_slot != "0"){

                // for($x=0; $x < $diff; $x+=$donor_cal->income_slot){
                //     $doncaldetl = new DonationDetail;
                //     $doncaldetl->donor_id = Auth::user()->id;
                //     $doncaldetl->donation_cal_id = $donor_cal->id;
                //     $doncaldetl->date = Carbon::now()->subMonth($sub)->addDays($x);
                //     $doncaldetl->income_amount = $donor_cal->income_amount;
                //     $doncaldetl->income_slot = $donor_cal->income_slot;
                //     $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                //     $doncaldetl->available_for_donation = $totaltran;
                //     $doncaldetl->save();
                // }

            // }
        }
    }

        $dondetails = DonationCalculator::with('donationdetail')->where('donor_id','=', Auth::user()->id)->get();
        return view('frontend.user.donationcal',compact('donor_cals','totaltran','totalotherdonation','availabledonation','dondetails'));


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





}
