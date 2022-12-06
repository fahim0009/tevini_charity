<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DonationCalculator;
use App\Models\DonationDetail;
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
}
