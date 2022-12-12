<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonationCalculator;
use App\Models\DonationDetail;
use App\Models\Usertransaction;
use Carbon\Carbon;
use Auth;

class DonationController extends Controller
{
    public function store(Request $request)
    {

        if(empty($request->income_amount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Income Amount field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->income_slot)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Slot field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->donation_percentage)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Donation Percent field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $data = new DonationCalculator;
        $data->income_amount = $request->income_amount;
        $data->income_slot = $request->income_slot;
        $data->donation_percentage = $request->donation_percentage;
        $data->donor_id = Auth::user()->id;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data created successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function update(Request $request)
    {

        if(empty($request->income_amount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Income Amount field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->income_slot)){
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

        if($donationamnt){
            $availabledonation = $totaltran - $donationamnt;

        }else{
            $availabledonation = 0;
        }


        $donor_cal = DonationCalculator::where('donor_id', Auth::user()->id)->first();

        if(empty($donor_cal)){

            $msg = "Fill this form for donation calculation";
            $dondetails = DonationDetail::where('donor_id','=', Auth::user()->id)->get();
            return view('frontend.user.donationcal',compact('totaltran','availabledonation','dondetails','msg'));


        }else{

        $donationdetails = DonationDetail::where('donor_id', Auth::user()->id)->orderBy('id', 'DESC')->first();

            if ($donationdetails) {
                $last_date = Carbon::parse($donationdetails->date);
                $diff_with_lastdate = $last_date->diffInDays($dt);

                        if($diff_with_lastdate >= $donor_cal->income_slot){
                        for($x=0; $x < $diff_with_lastdate; $x+=$donor_cal->income_slot){
                            $doncaldetl = new DonationDetail;
                            $doncaldetl->donor_id = Auth::user()->id;
                            $doncaldetl->donation_cal_id = $donor_cal->id;
                            $doncaldetl->date = $last_date->addDays($x);
                            $doncaldetl->income_amount = $donor_cal->income_amount;
                            $doncaldetl->income_slot = $donor_cal->income_slot;
                            $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                            $doncaldetl->available_for_donation = $totaltran;
                            $doncaldetl->save();
                        }
                    }


            } else {

                for($x=0; $x < $diff; $x+=$donor_cal->income_slot){
                    $doncaldetl = new DonationDetail;
                    $doncaldetl->donor_id = Auth::user()->id;
                    $doncaldetl->donation_cal_id = $donor_cal->id;
                    $doncaldetl->date = Carbon::now()->subMonth(8)->addDays($x);
                    $doncaldetl->income_amount = $donor_cal->income_amount;
                    $doncaldetl->income_slot = $donor_cal->income_slot;
                    $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                    $doncaldetl->available_for_donation = $totaltran;
                    $doncaldetl->save();
                }

            }
    }

        $dondetails = DonationDetail::where('donor_id','=', Auth::user()->id)->get();
        return view('frontend.user.donationcal',compact('donor_cal','totaltran','availabledonation','dondetails'));


    }





}
