<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\StandingDonation;
use App\Models\DonationCalculator;
use App\Models\DonationDetail;
use App\Models\StandingdonationDetail;
use App\Models\OtherDonation;
use App\Models\Charity;
use App\Models\User;
use App\Models\Donation;
use App\Models\OverdrawnRecord;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use App\Mail\TopupReport;
use App\Mail\DonationReport;
use App\Mail\DonationstandingReport;
use App\Mail\DonerReport;
use App\Mail\DonationreportCharity;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\Http;

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

        if(empty($first_date->date)){$start_date=$dt->toDateString();}else{$start_date=$first_date->date;}

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
        return view('frontend.user.donationcal',compact('donor_cals','tevini_donation','otherdonation','availabledonation'));


    }

    public function otherdonationDetails()
    {

        $data = OtherDonation::where([
            ['donor_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        return view('frontend.user.otherdonation',compact('data'));

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


    // standing order start 

    public function userStantingDonationStore(Request $request)
    {

        
        $userid = $request->userid;

        if(empty($request->charity_id)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select beneficiary field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->amount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill amount field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(($request->standard == "true") && ($request->payments_type == "1") && (empty($request->number_payments))){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill number of payments field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if($request->c_donation == "false"){
            $message ="<div class='alert alert-danger'>Please accept condition.</div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = new StandingDonation;
        $data->user_id = $userid;
        $data->charity_id = $request->charity_id;
        $data->amount = $request->amount;
        $data->currency = "GBP";
        $data->ano_donation = $request->ano_donation;
        $data->standing_order = $request->standard;
        $data->payments = $request->payments_type;
        $data->number_payments = $request->number_payments;
        $data->payment_made = 0;
        $data->starting = $request->starting;
        $data->interval = $request->interval;   
        $data->charitynote = $request->charitynote;
        $data->mynote = $request->mynote;
        $data->notification = 1;
        $data->status = 0;

        if($data->save()){

            // $utransaction = new Usertransaction();
            // $utransaction->t_id = time() . "-" . Auth::user()->id;
            // $utransaction->user_id = Auth::user()->id;
            // $utransaction->charity_id = $request->charity_id;
            // $utransaction->donation_id = $data->id;
            // $utransaction->t_type = "Out";
            // $utransaction->amount =  $request->amount;
            // $utransaction->title =  "Donation request confirmation";
            // $utransaction->status =  1;
            // $utransaction->save();

            // $user = User::find(Auth::user()->id);
            // $user->decrement('balance',$request->amount);
            // $user->save();

            // $charity = Charity::find($request->charity_id);
            // $charity->increment('balance',$request->amount);
            // $charity->save();

            $user = User::where('id',$userid)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $charity = Charity::where('id',$request->charity_id)->first();
            $donation = StandingDonation::where('id',$data->id)->first();

            // charity mail
            // $pdf = PDF::loadView('invoices.donation_report_charity', compact('user','charity','donation'));
            // $output = $pdf->output();
            // file_put_contents(public_path().'/invoices/'.'Donation-report-charity#'.$charity->id.'.pdf', $output);
            // $array['file'] = public_path().'/invoices/Donation-report-charity#'.$charity->id.'.pdf';
            // $array['file_name'] = 'Donation-report-charity#'.$charity->id.'.pdf';
            // $array['cc'] = $contactmail;
            // $array['charity'] = $charity;
            // $array['user'] = $user;
            // $email = $charity->email;

            // Mail::to($email)
            // ->cc($contactmail)
            // ->send(new DonationreportCharity($array));


            $array['name'] = $user->name;
            $array['donation'] = $donation;
            $array['cc'] = $contactmail;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['amount'] = $request->amount;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

            Mail::to($email)
            ->send(new DonationstandingReport($array));

            
            Mail::to($contactmail)
            ->send(new DonationstandingReport($array));

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Standing order donation submited Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }


    }

    public function donationStanding(Request $request)
    {
        if ($request->ajax()) {
            $query = StandingDonation::with(['user', 'charity'])->orderBy('id', 'desc');

            return DataTables::of($query)
                ->addColumn('donor', fn($d) => $d->user->name)
                ->addColumn('beneficiary', fn($d) => $d->charity->name)
                ->addColumn('amount', fn($d) => $d->amount)
                ->addColumn('anonymous', fn($d) => $d->ano_donation == "true" ? 'Yes' : 'No')
                ->addColumn('payments', function ($d) {
                    return $d->payments == 1 ? $d->number_payments : "Continuous payments";
                })
                ->addColumn('view', function ($d) {
                    return '<a href="'.route('singlestanding', $d->id).'">
                                <i class="fa fa-eye" style="color:#09a311;font-size:16px;"></i>
                            </a>';
                })
                ->addColumn('status_switch', function ($d) {
                    $checked = $d->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch">
                                <input class="form-check-input standingdnstatus" type="checkbox"
                                    data-id="'.$d->id.'" '.$checked.' >
                            </div>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at?->format('d/m/Y'))
                ->rawColumns(['view', 'status_switch'])
                ->make(true);
        }
        $donation = StandingDonation::all();
        return view('donor.standing',compact('donation'));
    }

    public function activeStandingdnsn(Request $request)
    {

        if($request->status==1){
            $active = StandingDonation::find($request->id);
            $active->status = $request->status;
            $active->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Active Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $deactive = StandingDonation::find($request->id);
            $deactive->status = $request->status;
            $deactive->save();
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Inactive Successfully.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }

    }

    public function activeStandinguser(Request $request)
    {

        if($request->status==1){
            $active = StandingDonation::where([
                ['id','=', $request->id],
                ['user_id','=', Auth::user()->id],
            ])->first();
            $active->status = $request->status;
            $active->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Active Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $deactive = StandingDonation::where([
                ['id','=', $request->id],
                ['user_id','=', Auth::user()->id],
            ])->first();
            $deactive->status = $request->status;
            $deactive->save();
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Inactive Successfully.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }

    }

    public function stdTest()
    {
      
        $current_date = now()->format('Y-m-d'); // current date with server format

        $activestand_orders = StandingDonation::where('status', '=' ,'1')->orderBy('id','DESC')->get();

        
        foreach($activestand_orders as $activestand_order)
        {
            
            $donationdetails = StandingdonationDetail::where('standing_donation_id', '=' , $activestand_order->id)->orderBy('id', 'desc')->first();

                if(isset($donationdetails)){
                    $start_date = $donationdetails->instalment_date;
                }else{
                    $start_date = $activestand_order->starting;
                }

                $start_date_carbon = Carbon::parse($start_date);
                $instalment_date_carbon = $start_date_carbon->addMonths($activestand_order->interval);
                $instalment_date = $instalment_date_carbon->format('Y-m-d');
                
                if($activestand_order->payments == 2){


                    if (($current_date >= $instalment_date)) {

                                $doncaldetl = new StandingdonationDetail;
                                $doncaldetl->standing_donation_id = $activestand_order->id;
                                $doncaldetl->user_id = $activestand_order->user_id;
                                $doncaldetl->charity_id = $activestand_order->charity_id;
                                $doncaldetl->amount = $activestand_order->amount;
                                $doncaldetl->instalment_date = $instalment_date;
                                $doncaldetl->instalment_mode = "continuous";
                                $doncaldetl->status = 0;
                                $doncaldetl->save();

                                $utransaction = new Usertransaction();
                                $utransaction->t_id = time() . "-" . $activestand_order->user_id;
                                $utransaction->user_id = $activestand_order->user_id;
                                $utransaction->charity_id = $activestand_order->charity_id;
                                $utransaction->standing_donationdetails_id = $activestand_order->id;
                                $utransaction->t_type = "Out";
                                $utransaction->amount =   $activestand_order->amount;
                                $utransaction->title =  "Standing order donation";
                                $utransaction->status =  1;
                                $utransaction->save();

                                $user = User::find($activestand_order->user_id);
                                $user->decrement('balance',$activestand_order->amount);
                                $user->save();

                                // card balance update
                                if (isset($user->CreditProfileId)) {
                                    $CreditProfileId = $user->CreditProfileId;
                                    $CreditProfileName = $user->name;
                                    $AvailableBalance = 0 - $activestand_order->amount;
                                    $comment = "Standing order donation";
                                    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                                        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                                            'CreditProfileId' => $CreditProfileId,
                                            'CreditProfileName' => $CreditProfileName,
                                            'AvailableBalance' => $AvailableBalance,
                                            'comment' => $comment,
                                        ]);
                                }
                                // card balance update end


        
                                $charity = Charity::find($activestand_order->charity_id);
                                $charity->increment('balance',$activestand_order->amount);
                                $charity->save();

                    }

                }elseif($activestand_order->payments == 1){

                    if (($current_date >= $instalment_date) && ($activestand_order->payment_made < $activestand_order->number_payments)) {

                        $doncaldetl = new StandingdonationDetail;
                        $doncaldetl->standing_donation_id = $activestand_order->id;
                        $doncaldetl->user_id = $activestand_order->user_id;
                        $doncaldetl->charity_id = $activestand_order->charity_id;
                        $doncaldetl->amount = $activestand_order->amount;
                        $doncaldetl->instalment_date = $instalment_date;
                        $doncaldetl->instalment_mode = "Fiexed";
                        $doncaldetl->status = 0;
                        $doncaldetl->save();

                        $utransaction = new Usertransaction();
                        $utransaction->t_id = time() . "-" . $activestand_order->user_id;
                        $utransaction->user_id = $activestand_order->user_id;
                        $utransaction->charity_id = $activestand_order->charity_id;
                        $utransaction->standing_donationdetails_id = $activestand_order->id;
                        $utransaction->t_type = "Out";
                        $utransaction->amount =   $activestand_order->amount;
                        $utransaction->title =  "Standing order donation";
                        $utransaction->status =  1;
                        $utransaction->save();

                        $user = User::find($activestand_order->user_id);
                        $user->decrement('balance',$activestand_order->amount);
                        $user->save();

                        // card balance update
                        if (isset($user->CreditProfileId)) {
                            $CreditProfileId = $user->CreditProfileId;
                            $CreditProfileName = $user->name;
                            $AvailableBalance = 0 - $activestand_order->amount;
                            $comment = "Standing order donation";
                            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                                    'CreditProfileId' => $CreditProfileId,
                                    'CreditProfileName' => $CreditProfileName,
                                    'AvailableBalance' => $AvailableBalance,
                                    'comment' => $comment,
                                ]);
                        }
                        // card balance update end

                        $charity = Charity::find($activestand_order->charity_id);
                        $charity->increment('balance',$activestand_order->amount);
                        $charity->save();

                        $standing_order = StandingDonation::find($activestand_order->id);
                        $standing_order->increment('payment_made',1);
                        $standing_order->save();

                 }


                }

        }
        return "Run";
    }



    public function singledonationStanding($id)
    {

        $singleStddonation = StandingdonationDetail::where('standing_donation_id', '=' , $id)->orderBy('id','DESC')->get();
        return view('donor.single_standing',compact('singleStddonation'));

    }

    
    public function usersingleStanding($id)
    {

        $singleStddonation = StandingdonationDetail::where([
            ['standing_donation_id','=', $id],
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        return view('frontend.user.singlestandingrecord',compact('singleStddonation'));

    }

    




}
