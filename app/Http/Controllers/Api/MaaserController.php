<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\DonationCalculator;
use App\Models\DonationDetail;
use App\Models\OtherDonation;
use App\Models\Usertransaction;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Http;

class MaaserController extends Controller
{
    public function donationCal(Request $request)
    {
        // donation calculaton start
        $dt = Carbon::now();
        $sub = $dt->month - 4; // sub month will change in every month

        $first_date = DonationDetail::where('donor_id', Auth::user()->id)->orderBy('date','ASC')->first();

        if(empty($first_date->date)){$start_date=$dt->toDateString();}else{$start_date=$first_date->date;}

        $tevini_donation = Usertransaction::where([
            ['user_id','=', Auth::user()->id],
            ['t_type','=', 'Out'],
            ['status','=', '1']
        ])->whereDate('created_at','>=',$start_date)->sum('amount');


        $donationamnt = DonationDetail::where('donor_id', Auth::user()->id)->sum('donation_amount');

        $otherdonation = OtherDonation::where('donor_id', Auth::user()->id)->whereDate('donation_date','>=',$start_date)->sum('d_amount');


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

        $responseArray = [
            'status'=>'ok',
            'donor_cals'=>$donor_cals,
            'tevini_donation'=>$tevini_donation,
            'otherdonation'=>$otherdonation,
            'availabledonation'=>$availabledonation
        ]; 
        return response()->json($responseArray,200);

    }

    public function oneoffDonation(Request $request)
    {
        $ostart_date = $request->ostart_date;
        $oincome_amount = $request->oincome_amount;
        $oincome_title = $request->oincome_title;
        $odonation_percentage = $request->odonation_percentage;

        if(empty($ostart_date)){
            $success['message'] = 'Please fill Donation Date field..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
        }

        if(empty($oincome_amount)){
            
            $success['message'] = 'Please fill Donation Amount field..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
        }

        if(empty($oincome_title)){
            $success['message'] = 'Please fill Donation Title field..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
        }


        if(empty($odonation_percentage)){
            
            $success['message'] = 'Please Choose Your Percentage.';
                return response()->json(['success'=>false,'response'=> $success], 202);
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



        $success['message'] = 'One-off income added successfully.';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);

    }


    public function otherDonationStore(Request $request)
    {

        if(empty($request->d_amount)){
            $success['message'] = 'Please fill Donation Amount field..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
        }

        if(empty($request->donation_date)){
            $success['message'] = 'Please fill Donation Date field..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
        }

        if(empty($request->d_title)){
            $success['message'] = 'Please fill Donation Title field..';
                return response()->json(['success'=>false,'response'=> $success], 202);
                exit();
        }

        $data = new OtherDonation;
        $data->d_title = $request->d_title;
        $data->donation_date = $request->donation_date;
        $data->d_amount = $request->d_amount;
        $data->donor_id = Auth::user()->id;
        if($data->save()){
            $success['message'] = 'Data create successfully.';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }

    }

    public function store(Request $request)
    {
        
        // dd($request->all());

        $alldata = json_decode($request->alldata, true); 

        foreach ($alldata as $item){
            if($item['start_date'] == "" || $item['income_amount'] == "" || $item['income_title'] == "" || $item['income_slot'] == "" || $item['donation_percentage'] == ""){
                
                $success['message'] = 'Please fill all field..';
                    return response()->json(['success'=>false,'response'=> $success], 202);
                    exit();
            }
        }


        foreach ($alldata as $item)
        {
            $data = new DonationCalculator;
            $data->start_date = $item['start_date'];
            $data->income_amount = $item['income_amount'];
            $data->income_title = $item['income_title'];
            $data->income_slot = $item['income_slot'];
            $data->donation_percentage = $item['donation_percentage'];
            $data->status = 1;
            $data->donor_id = Auth::user()->id;
            $data->created_by = Auth::user()->id;
            $data->save();

        }

        $success['message'] = 'Data created successfully.';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);

    }


    public function DcalUpdate(Request $request)
    {

        $alldata = json_decode($request->alldata, true); 

        foreach ($alldata as $item){
            if($item['start_date'] == "" || $item['income_amount'] == "" || $item['income_title'] == "" || $item['income_slot'] == "" || $item['donation_percentage'] == ""){
                
                $success['message'] = 'Please fill all field..';
                    return response()->json(['success'=>false,'response'=> $success], 202);
                    exit();
            }
        }



        foreach ($alldata as $item)
        {
            if(isset($item['donorcal_id'])){

            $data =  DonationCalculator::findOrFail($item['donorcal_id']);
            $data->start_date = $item['start_date'];
            $data->income_amount = $item['income_amount'];
            $data->income_title = $item['income_title'];
            $data->income_slot = $item['income_slot'];
            $data->donation_percentage = $item['donation_percentage'];
            $data->donor_id = Auth::user()->id;
            $data->updated_by = Auth::user()->id;
            $data->save();

            }else{

            $data = new DonationCalculator;
            $data->start_date = $item['start_date'];
            $data->income_amount = $item['income_amount'];
            $data->income_title = $item['income_title'];
            $data->income_slot = $item['income_slot'];
            $data->donation_percentage = $item['donation_percentage'];
            $data->status = 1;
            $data->donor_id = Auth::user()->id;
            $data->created_by = Auth::user()->id;
            $data->save();

            }

        }

        $success['message'] = 'Data updated successfully.';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);


    }
}
