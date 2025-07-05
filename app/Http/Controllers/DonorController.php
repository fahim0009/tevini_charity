<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Provoucher;
use App\Models\User;
use App\Models\Donation;
use App\Models\StandingDonation;
use App\Models\StandingdonationDetail;
use App\Models\OverdrawnRecord;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Charity;
use App\Models\ContactMail;
use App\Mail\TopupReport;
use App\Mail\DonationReport;
use App\Mail\DonationstandingReport;
use App\Mail\DonerReport;
use App\Mail\DonationreportCharity;
use App\Mail\DonorCustomMail;
use App\Mail\TDFTransfer;
use App\Models\ExpectedGiftAid;
use App\Models\TdfTransaction;
use PDF;
use Hash;
use Auth;
use Illuminate\Support\Facades\Http;

class DonorController extends Controller
{
    public function index()
    {
        return view('donor.index');
    }

    public function addDonor()
    {
        $users = User::where('is_type', 'user')
            ->select('id', 'name', 'surname', 'email', 'phone', 'accountno', 'town', 'balance', 'overdrawn_amount', 'email_verified_at')
            ->with(['usertransaction' => function ($query) {
                $query->where('status', 1)->orWhere('pending', 1)->orderBy('id', 'DESC');
            }])
            ->orderBy('id', 'DESC')
            ->get();

        return view('donor.adddonor', compact('users'));
    }

    public function donorStore(Request $request)
    {
        $email=User::where('email', $request->email)->count();
        if( $email == 1){
            $message ="This email already used!!";
            return back()->with('error', $message);
            exit();
        }


        $this->validate($request,[
            'title' => 'required',
            'fname' => 'required',
            'surname' => 'required',
            'email' => 'required|email'
        ]);

        $user = new User;
        $user->prefix_name = $request->prefix_name;
        $user->name = $request->fname;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->title = $request->title;
        $user->street = $request->street;
        $user->postcode = $request->postcode;
        $user->houseno = $request->houseno;
        $user->town = $request->town;
        if($request->accno){
        $user->accountno = $request->accno;
        $user->status = "1";
        }
        if(empty($request->balance)){
        $user->balance = 00;
        }else{
        $user->balance = $request->balance;
        }
        $user->password = Hash::make('123456');
        if($user->save()){

            $message ="Donor Created Successfully";
            return back()->with('message', $message);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }

    public function donorEdit($id)
    {

        $users = User::where('id','=' ,decrypt($id))->first();
        return view('donor.editdonor', compact('users'));

        // return response()->json($info);
    }

    public function donorUpdate(Request $request, $id)
    {

        $chkemail = User::where('email','=', $request->email)->whereNotIn('id', [$id])->count();


        if( $chkemail == 1){
            $message ="This email has already exists.";
            return redirect()->route('donor')->with(['status'=> 303,'error'=> $message]);

        }

        if ($request->password) {
            if ($request->password != $request->cpassword) {
                $message ="Password doesn't match!!";
                return redirect()->route('donor')->with(['status'=> 303,'error'=> $message]);
            }
        }



        $user = User::findOrFail($id);

        $user->prefix_name = $request->prefix_name;

        if ($request->profile_type == "Company") {
            $user->name = $request->company_name;
            $user->surname = $request->company_last_name;
        } else {
            $user->name = $request->fname;
            $user->surname = $request->surname;
        }
        
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->title = $request->title;
        $user->street = $request->street;
        $user->postcode = $request->postcode;
        $user->houseno = $request->houseno;
        $user->address_third_line = $request->address_third_line;
        $user->town = $request->town;
        $user->accountno = $request->accountno;

        if ($request->password) {
            $user->password= Hash::make($request->password);
        }
        if($user->save()){

            $message ="Donor Update Successfully";

        return redirect()->route('donor')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }

    public function deleteDonor(Request $request)
    {
        if( User::destroy($request->donorId)){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donor deleted successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Delete Fail...</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
    }


    public function topup($id,$amount)
    {
        $topup = User::where('id','=', $id)->first();
        return view('donor.topup')
        ->with('topup',$topup)
        ->with('amount',$amount);
    }

    public function topupStore(Request $request)
    {
        if(empty($request->gbalance)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill amount fill.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $donationBy = $request->donationBy;

        $user = User::find($request->topupid);
        $user->balance = $user->balance + $request->balance;
        if($user->save()){

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 + $request->balance;
                $comment = "Donor Topup";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end


            if ($request->commission != '0') {
                $topup = new Commission();
                $topup->user_id = $request->topupid;
                $topup->commission = $request->commission;
                $topup->save();
            }
            $transaction = new Transaction();
            $transaction->t_id = time() . "-" . $request->topupid;
            $transaction->user_id = $request->topupid;
            $transaction->t_type = "In";
            $transaction->name = $request->source;
            $transaction->commission = $request->commission;
            $transaction->amount = $request->gbalance;
            $transaction->note = $request->note;
            $transaction->status = "1";
            $transaction->save();

            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . $request->topupid;
            $utransaction->user_id = $request->topupid;
            $utransaction->source = $request->source;
            $utransaction->t_type = "In";
            $utransaction->commission = $request->commission;
            $utransaction->amount = $request->balance;
            $utransaction->note = $request->note;
            $utransaction->donation_by = $request->donationBy;
            if($request->gift == "true"){
            $utransaction->gift =  1;
            }
            $utransaction->title = 'Credit';
            $utransaction->status =  1;
            $utransaction->save();

            if($request->gift == "true"){
                $expgiftaidamnt = $request->balance * 25/100;
                $donorgiftaid = User::find($request->topupid);
                $donorgiftaid->expected_gift_aid = $donorgiftaid->expected_gift_aid + $expgiftaidamnt;
                $donorgiftaid->save();

                $expgiftaidtran = new ExpectedGiftAid();
                $expgiftaidtran->user_id = $request->topupid;
                $expgiftaidtran->transaction_id = $transaction->id;
                $expgiftaidtran->usertransaction_id = $utransaction->id;
                $expgiftaidtran->amount = $request->balance;
                $expgiftaidtran->gift_amount = $expgiftaidamnt;
                $expgiftaidtran->save();
            }

            if($request->cleargift == "true"){
                $cleargiftaid = User::find($request->topupid);
                $cleargiftaid->expected_gift_aid = $cleargiftaid->expected_gift_aid - $request->balance;
                $cleargiftaid->save();
                }

            if($request->receipt == 'true'){
                $user = User::where('id',$request->topupid)->first();

                $contactmail = ContactMail::where('id', 1)->first()->name;

                $balance = $request->gbalance;
                $source = $request->source;
                $title = "DONATION RECEIPT";
                $pdf = PDF::loadView('invoices.topup_report', compact('balance','source','user','donationBy','title'));
                $output = $pdf->output();
                file_put_contents(public_path().'/invoices/'.'Donation-report#'.$user->id.'.pdf', $output);
                $array['file'] = public_path().'/invoices/Donation-report#'.$user->id.'.pdf';
                $array['file_name'] = 'Donation-report#'.$user->id.'.pdf';
                $array['cc'] = $contactmail;
                $array['name'] = $user->name;
                $array['email'] = $user->email;
                $array['phone'] = $user->phone;
                $email = $user->email;
                $array['balance'] = $request->balance;
                $array['gbalance'] = $request->gbalance;
                $array['commission'] = $request->commission;
                $array['source'] = $request->source;

                Mail::to($email)
                ->cc($contactmail)
                ->send(new TopupReport($array));

                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully & receipt sent to donor mail.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }
    public function userTopReportShowinAdmin($id)
    {
        $transaction = Usertransaction::where('id', $id)->first();
        $user = User::where('id',$transaction->user_id)->first();
        
        $balance = $transaction->amount;
        $commission = $transaction->commission ?? 0;
        $source = $transaction->source;
        return view('donor.topupreportshow', compact('balance','source','user','commission','transaction'));
    }


    // Monthly statement in admin
    public function userReportinAdmin(Request $request, $id)
    {
        $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
        $user = User::find($id);
        $donor_id = $user->id;
        if(!empty($request->fromdate) && !empty($request->todate)){
             $fromDate = $request->fromdate;
             $toDate   = $request->todate;
             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['status','=', '1']
             ])->orwhere([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['pending','=', '0']
                 ])->orderBy('id','DESC')->get();
         }else{
             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['status','=', '1']
                 ])->orwhere([
                ['user_id','=', $id],
                ['pending','=', '0']
                ])->orderBy('id','DESC')->get();
             $fromDate = "";
             $toDate   = "";
         }

         return view('donor.report',compact('report','fromDate','toDate','donor_id','user','tamount'));

    }



    public function userTopReportinAdmin(Request $request, $id)
    {
        $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
        $user = User::find($id);
        $donor_id = $user->id;
        if(!empty($request->fromdate) && !empty($request->todate)){
             $fromDate = $request->fromdate;
             $toDate   = $request->todate;
             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['t_type','=', 'In'],
                 ['status','=', '1']
             ])->orwhere([
                 ['user_id','=', $id],
                 ['created_at', '>=', $fromDate],
                 ['created_at', '<=', $toDate.' 23:59:59'],
                 ['t_type','=', 'In'],
                 ['pending','=', '0']
                 ])->orderBy('id','DESC')->get();
         }else{
             $report = Usertransaction::where([
                 ['user_id','=', $id],
                 ['t_type','=', 'In'],
                 ['status','=', '1']
                 ])->orwhere([
                ['user_id','=', $id],
                ['t_type','=', 'In'],
                ['pending','=', '0']
                ])->orderBy('id','DESC')->get();
             $fromDate = "";
             $toDate   = "";
         }

         return view('donor.topupreport',compact('report','fromDate','toDate','donor_id','user','tamount'));

    }


    public function userReportMailinAdmin(Request $request)
    {
            $id = $request->user_id;
            $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
            if(!empty($request->input('fromdate')) && !empty($request->input('todate'))){
                $fromDate = $request->input('fromdate');
                $toDate   = $request->input('todate');
                $report = Usertransaction::where([
                    ['user_id','=', $id],
                    ['created_at', '>=', $fromDate],
                    ['created_at', '<=', $toDate.' 23:59:59'],
                    ['status','=', '1']
                ])->orwhere([
                    ['user_id','=', $id],
                    ['pending','=', '0']
                    ])->orderBy('id','DESC')->get();

              }else{

                $report = Usertransaction::where([
                    ['user_id','=', $id],
                    ['status','=', '1']
                ])->orwhere([
                   ['user_id','=', $id],
                   ['pending','=', '0']
                   ])->orderBy('id','DESC')->get();


                $fromDate = "";
                $toDate   = "";
            }
            $user = User::find($id);
            // donor balance
            $userTransactionBalance = Usertransaction::selectRaw('
                    SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                    SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
                ')
                ->where([
                    ['user_id','=', $user->id],
                    ['status','=', '1']
                ])->orwhere([
                    ['user_id','=',  $user->id],
                    ['pending','=', '1']
                ])
                ->first();
            // donor balance end

            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['cc'] = $contactmail;
            $pdf = PDF::loadView('invoices.donor_report', compact('report','fromDate','toDate','user','tamount'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'Report#'.$id.'.pdf', $output);
            $array['name'] = $user->name;
            $array['userbalance'] =  number_format($userTransactionBalance->balance, 2);
            $array['view'] = 'mail.donorreport';
            $array['subject'] = 'Monthly statement';
            $array['from'] = 'info@tevini.co.uk';
            $array['content'] = 'Hi, Your donation report has been placed';
            $array['file'] = public_path().'/invoices/Report#'.$id.'.pdf';
            $array['file_name'] = 'Report#'.$id.'.pdf';
            $array['subjectsingle'] = 'Report Placed - '.$id;

            Mail::to($user->email)->queue(new DonerReport($array));

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Mail sent successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);


    }


    public function userTopupReportMailinAdmin(Request $request)
    {
        
            $tranid = $request->transaction_id;
            $trandata = Usertransaction::where('id','=', $tranid)->first();
            $user = User::where('id',$request->user_id)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;

                $balance = $trandata->amount + $trandata->commission;
                $source = $trandata->source;
                $donationBy = $trandata->donation_by;
                $title = "DONATION RECEIPT";
                $pdf = PDF::loadView('invoices.topup_report', compact('balance','source','user','donationBy','title'));
                $output = $pdf->output();
                file_put_contents(public_path().'/invoices/'.'Donation-report#'.$user->id.'.pdf', $output);
                $array['file'] = public_path().'/invoices/Donation-report#'.$user->id.'.pdf';
                $array['file_name'] = 'Donation-report#'.$user->id.'.pdf';
                $array['cc'] = $contactmail;
                $array['name'] = $user->name;
                $array['email'] = $user->email;
                $array['phone'] = $user->phone;
                $email = $user->email;
                $array['balance'] = $request->balance;
                $array['gbalance'] = $request->gbalance;
                $array['commission'] = $request->commission;
                $array['source'] = $request->source;

                Mail::to($email)
                ->cc($contactmail)
                ->send(new TopupReport($array));

                $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully & receipt sent to donor mail.</b></div>";
                return response()->json(['status'=> 300,'message'=>$message]);
    }

    // report to multiple donor

    public function multiUserreport2(Request $request)
    {
        $checkAll = $request->checkAll;
        $donorIds = $request->donorIds;
        $fromDate = $request->fromdate;
        $toDate   = $request->todate;

        if(empty($request->donorIds)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select donor.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->fromdate)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select From Date.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->todate)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select To Date.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if ($checkAll == "all") {

            $activedonorIds = User::where('is_type', 'user')->where('status', 1)->get();
            foreach($activedonorIds as $duser)
            {

                $tamount = Usertransaction::where('user_id','=', $duser->id)->where('status','=', '1')->orderBy('id','DESC')->get();
                if(!empty($fromDate) && !empty($toDate)){

                    $report = Usertransaction::where([
                        ['user_id','=', $duser->id],
                        ['created_at', '>=', $fromDate],
                        ['created_at', '<=', $toDate.' 23:59:59'],
                        ['status','=', '1']
                    ])->orwhere([
                        ['user_id','=', $duser->id],
                        ['pending','=', '0']
                        ])->orderBy('id','DESC')->get();
                }else{
                    $report = Usertransaction::where([
                        ['user_id','=', $duser->id],
                        ['status','=', '1']
                    ])->orwhere([
                    ['user_id','=', $duser->id],
                    ['pending','=', '0']
                    ])->orderBy('id','DESC')->get();
                    $fromDate = "";
                    $toDate   = "";
                }
                $user = User::find($duser->id);

                // donor balance
                $userTransactionBalance = Usertransaction::selectRaw('
                        SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                        SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
                    ')
                    ->where([
                        ['user_id','=', $user->id],
                        ['status','=', '1']
                    ])->orwhere([
                        ['user_id','=',  $user->id],
                        ['pending','=', '1']
                    ])
                    ->first();
                // donor balance end



                $contactmail = ContactMail::where('id', 1)->first()->name;
                $array['cc'] = $contactmail;
                $pdf = PDF::loadView('invoices.donor_report', compact('report','fromDate','toDate','user','tamount'));
                $output = $pdf->output();
                file_put_contents(public_path().'/invoices/'.'Report#'.$duser->id.'.pdf', $output);
                $array['userbalance'] =  number_format($userTransactionBalance->balance, 2);
                $array['name'] = $user->name;
                $array['view'] = 'mail.donorreport';
                $array['subject'] = 'Monthly statement';
                $array['from'] = 'info@tevini.co.uk';
                $array['content'] = 'Hi, Your donation report has been placed';
                $array['file'] = public_path().'/invoices/Report#'.$duser->id.'.pdf';
                $array['file_name'] = 'Report#'.$duser->id.'.pdf';
                $array['subjectsingle'] = 'Report Placed - '.$duser->id;
                
                if ($user->email_verified_at) {
                    Mail::to($user->email)->queue(new DonerReport($array));
                }

            }
            
        } else {
            
            foreach($donorIds as $id)
            {

                $tamount = Usertransaction::where('user_id','=', $id)->where('status','=', '1')->orderBy('id','DESC')->get();
                if(!empty($fromDate) && !empty($toDate)){

                    $report = Usertransaction::where([
                        ['user_id','=', $id],
                        ['created_at', '>=', $fromDate],
                        ['created_at', '<=', $toDate.' 23:59:59'],
                        ['status','=', '1']
                    ])->orwhere([
                        ['user_id','=', $id],
                        ['pending','=', '0']
                        ])->orderBy('id','DESC')->get();
                }else{
                    $report = Usertransaction::where([
                        ['user_id','=', $id],
                        ['status','=', '1']
                    ])->orwhere([
                    ['user_id','=', $id],
                    ['pending','=', '0']
                    ])->orderBy('id','DESC')->get();
                    $fromDate = "";
                    $toDate   = "";
                }

                // donor balance
                $userTransactionBalance = Usertransaction::selectRaw('
                        SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                        SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
                    ')
                    ->where([
                        ['user_id','=', $id],
                        ['status','=', '1']
                    ])->orwhere([
                        ['user_id','=', $id],
                        ['pending','=', '1']
                    ])
                    ->first();
                // donor balance end


                $user = User::find($id);
                $contactmail = ContactMail::where('id', 1)->first()->name;
                $array['cc'] = $contactmail;
                $pdf = PDF::loadView('invoices.donor_report', compact('report','fromDate','toDate','user','tamount'));
                $output = $pdf->output();
                file_put_contents(public_path().'/invoices/'.'Report#'.$id.'.pdf', $output);
                $array['userbalance'] = number_format($userTransactionBalance->balance, 2);
                $array['name'] = $user->name;
                $array['view'] = 'mail.donorreport';
                $array['subject'] = 'Monthly statement';
                $array['from'] = 'info@tevini.co.uk';
                $array['content'] = 'Hi, Your donation report has been placed';
                $array['file'] = public_path().'/invoices/Report#'.$id.'.pdf';
                $array['file_name'] = 'Report#'.$id.'.pdf';
                $array['subjectsingle'] = 'Report Placed - '.$id;

                // if ($user->email_verified_at) {
                //     Mail::to($user->email)->queue(new DonerReport($array));
                // }
                Mail::to($user->email)->cc($contactmail)->send(new DonerReport($array));

            }
        }
        
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Mail sent successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);


    }

    public function multiUserreport_new(Request $request)
    {

        ini_set('max_execution_time', 1800); // 30 minutes
        set_time_limit(1800);

        $checkAll = $request->checkAll;
        $donorIds = $request->donorIds;
        $fromDate = $request->fromdate;
        $toDate   = $request->todate;

        if ($checkAll != 'all' && empty($donorIds)) {
            return response()->json(['status' => 303, 'message' => $this->alert('danger', 'Please select donor.')]);
        }

        if (empty($fromDate)) {
            return response()->json(['status' => 303, 'message' => $this->alert('danger', 'Please select From Date.')]);
        }

        if (empty($toDate)) {
            return response()->json(['status' => 303, 'message' => $this->alert('danger', 'Please select To Date.')]);
        }

        $contactmail = ContactMail::where('id', 1)->value('name');

        $processUser = function ($user) use ($fromDate, $toDate, $contactmail) {
            $tamount = Usertransaction::where('user_id', $user->id)
                ->where('status', 1)
                ->orderBy('id', 'DESC')
                ->get();

            $report = Usertransaction::where('user_id', $user->id)
                ->where(function ($query) use ($fromDate, $toDate) {
                    $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59'])
                        ->where('status', 1);
                })
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('pending', 0);
                })
                ->orderBy('id', 'DESC')
                ->get();

            $userTransactionBalance = Usertransaction::selectRaw('
                    SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                    SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
                ')
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('pending', 1);
                })
                ->first();

            $pdf = PDF::loadView('invoices.donor_report', compact('report', 'fromDate', 'toDate', 'user', 'tamount'));
            $filePath = public_path('/invoices/Report#' . $user->id . '.pdf');
            file_put_contents($filePath, $pdf->output());

            $array = [
                'cc' => $contactmail,
                'userbalance' => number_format($userTransactionBalance->balance, 2),
                'name' => $user->name,
                'view' => 'mail.donorreport',
                'subject' => 'Monthly statement',
                'from' => 'info@tevini.co.uk',
                'content' => 'Hi, Your donation report has been placed',
                'file' => $filePath,
                'file_name' => 'Report#' . $user->id . '.pdf',
                'subjectsingle' => 'Report Placed - ' . $user->id,
            ];

            if ($user->email_verified_at) {
                Mail::to($user->email)->queue(new DonerReport($array));
            }
        };

        if ($checkAll == "all") {
            User::where('is_type', 'user')->where('status', 1)->chunk(5, function ($users) use ($processUser) {
                foreach ($users as $user) {
                    $processUser($user);
                }
            });
        } else {
            foreach ($donorIds as $id) {
                $user = User::find($id);
                if ($user) {
                    $processUser($user);
                }
            }
        }

        return response()->json(['status' => 300, 'message' => $this->alert('success', 'Mail sent successfully.')]);
    }

    public function multiUserreport(Request $request)
    {
        $checkAll = $request->checkAll;
        $donorIds = $request->donorIds;
        $fromDate = $request->fromdate;
        $toDate   = $request->todate;

        if ($checkAll != 'all' && empty($donorIds)) {
            return response()->json(['status' => 303, 'message' => $this->alert('danger', 'Please select donor.')]);
        }

        if (empty($fromDate)) {
            return response()->json(['status' => 303, 'message' => $this->alert('danger', 'Please select From Date.')]);
        }

        if (empty($toDate)) {
            return response()->json(['status' => 303, 'message' => $this->alert('danger', 'Please select To Date.')]);
        }

        if ($checkAll == "all") {
            
            User::where('is_type', 'user')->where('status', 1)->chunk(100, function ($users) use ($fromDate, $toDate, $checkAll) {
                foreach ($users as $user) {
                    dispatch(new \App\Jobs\SendDonorReportJob($user->id, $fromDate, $toDate, $checkAll));
                }
            });
            
        } else {
            foreach ($donorIds as $id) {
                dispatch(new \App\Jobs\SendDonorReportJob($id, $fromDate, $toDate, $checkAll));
            }
        }

        return response()->json(['status' => 300, 'message' => $this->alert('success', 'Mail sent successfully.')]);
    }


    private function alert($type, $message)
    {
        return "<div class='alert alert-$type'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>$message</b></div>";
    }



    public function adminDonorGiving()
    {
        return view('donor.donorgiving');
    }

    public function userDonationShow()
    {
        // user balance calculation start
        $gettrans = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
        ])->orderBy('id','DESC')->get();

        $donorUpBalance = 0;

        foreach ($gettrans as $key => $tran) {
            if ($tran->t_type == "In") {
                $donorUpBalance = $donorUpBalance + $tran->amount;
            }elseif ($tran->t_type == "Out") {
                $donorUpBalance = $donorUpBalance - $tran->amount;
            } else {
                # code...
            }
        }
        // user balance calculation end
        return view('frontend.user.donation', compact('donorUpBalance'));
    }

    public function makeDonationAppView()
    {
        
        return view('frontend.user.donationapp');
    }
    public function makeDonationAppMessage()
    {
        return view('frontend.user.donationsuccess');
    }


    public function userDonationShowwithID($token)
    {
        // Get the full URL
        // $url = request()->url();
        // // Extract the last segment after the last '/'
        // $lastSegment = substr($url, strrpos($url, '/') + 1);
        // // Make a GET request to retrieve user data or perform actions
        // $response = Http::withToken($lastSegment)->get($url);

        // // Handle response
        // if ($response->successful()) {
        //     $userData = $response->json();
        //     // Process user data or perform actions
        // } else {
        //     // Handle failed authentication or other errors
        //     abort($response->status(), 'Unauthorized');
        // }


        // Retrieve the token from session or wherever you stored it
    $token = $token;

    // You can use the token to authenticate API requests
    // Example using GuzzleHttp:
    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', 'https://www.tevini.co.uk/user/make-donation', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    ]);

    $user = json_decode($response->getBody()->getContents());


        // $user = User::where('id', $id)->first();
        return view('frontend.user.donation', compact('user'));
    }

    public function tdfTransferAdmin($id)
    {
        $donor_id = $id;
        return view('donor.tdftransfer',compact('donor_id'));
    }

    public function tdfTransferStore(Request $request)
    {

        $donor_id = $request->donner_id;
        if(empty($request->tdfaccount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Account Field Required.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if($request->tdfamount < 18){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Minimum transfer is £18.00.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        if(empty($request->tdfamount)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Amount Field Required.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        // donor balance
        $userTransactionBalance = Usertransaction::selectRaw('
                SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
            ')
            ->where([
                ['user_id','=', $donor_id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=',  $donor_id],
                ['pending','=', '1']
            ])
            ->first();
        // donor balance end
        
        
        $overdrownlimit = User::where('id', $donor_id)->first()->overdrawn_amount;
        $donorBlanacewithLimit = $userTransactionBalance->balance + $overdrownlimit;

        if($request->tdfamount  > $donorBlanacewithLimit){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>You don't have enough balance to transfer.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        
        $data = new TdfTransaction();
        $data->issue_date = date('Y-m-d');
        $data->user_id = $donor_id;
        $data->tdfaccount = $request->tdfaccount;
        $data->tdf_amount = $request->tdfamount;
        $data->current_dollar_amount = $request->tdfamount;
        if ($data->save()) {

            $user = User::find($donor_id);
            $user->balance = $user->balance - $request->tdfamount;
            $user->save();

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $request->tdfamount;
                $comment = "Transfer to TDF";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".$donor_id;
            $udtransaction->user_id = $donor_id;
            $udtransaction->t_type = "Out";
            $udtransaction->amount =  $request->tdfamount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title ="Transfer to TDF";
            $udtransaction->status =  1;
            $udtransaction->save();

            $transaction = new Transaction();
            $transaction->t_id = time()."-".$donor_id;
            $transaction->user_id = $donor_id;
            $transaction->t_type = "Out";
            $transaction->amount =  $request->tdfamount;
            $transaction->note ="Transfer to TDF";
            $transaction->status =  1;
            $transaction->save();

            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['name'] = auth()->user()->name;
            $array['subject'] = 'Urgent request';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $email = auth()->user()->email;

            Mail::to($email)
                    ->cc($contactmail)
                    ->send(new TDFTransfer($array));


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Successfully transferred to TDF.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        } else {
            return response()->json(['status'=> 303,'message'=>'Server error!!']);
        }
    }

    public function userDonationAdmin($id)
    {
        $donor_id = $id;
        return view('donor.onlinedonation',compact('donor_id'));
    }

    public function userOnlineDonationStore(Request $request) 
    {
        

        if ($request->standard == "on") {
            
            $rules = [
                'userid' => 'required',
                'charity_id' => 'required',
                'payments_type' => 'required',
                'number_payments' => 'required_if:payments_type,1|integer',
                'starting' => 'required',
                'interval' => 'required',
                'amount' => 'required|integer',
                'confirm_donation' => 'required'
            ];
            $customMessages = [
                'userid.required' => 'Server error!!!',
                'charity_id.required' => 'Please select a charity.',
                'payments_type.required' => 'Please select payment type.',
                'number_payments.required' => 'Number of payment field is required',
                'starting.required' => 'Starting field is required.',
                'interval.required' => 'Interval field is required.',
                'confirm_donation.required' => 'Please check confirmed about your donation.',
                'required' => 'The :attribute field is required.',
            ];
            $this->validate($request, $rules, $customMessages);


            $userid = $request->userid;
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

                $user = User::where('id',$userid)->first();
                $contactmail = ContactMail::where('id', 1)->first()->name;
                $charity = Charity::where('id',$request->charity_id)->first();
                $donation = StandingDonation::where('id',$data->id)->first();


                $array['name'] = $user->name;
                $array['donation'] = $donation;
                $array['cc'] = $contactmail;
                $array['client_no'] = $user->accountno;
                $email = $user->email;
                $array['amount'] = $request->amount;
                $array['charity_note'] = $request->charitynote;
                $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

                Mail::to($email)
                ->cc($contactmail)
                ->send(new DonationstandingReport($array));
                
                // return redirect()->route('onlinedonation.appview')->with('success', 'Standing order donation submited Successfully.');

                // return view('frontend.user.donationsuccess', ['success' => 'Standing order donation submited Successfully.', 'userid' => $userid]);

                return view('frontend.user.donationsuccess')->with('success', 'Donation submited Successfully.')->with('userid', $userid);

            }

        } else {

            
            $rules = [
                'userid' => 'required',
                'charity_id' => 'required',
                'amount' => 'required|integer',
                'confirm_donation' => 'required'
            ];
            $customMessages = [
                'userid.required' => 'Server error!!!',
                'charity_id.required' => 'Please select a charity.',
                'confirm_donation.required' => 'Please check confirmed about your donation.',
                'required' => 'The :attribute field is required.',
            ];
            $this->validate($request, $rules, $customMessages);

            $userid = $request->userid;
            $data = new Donation;
            $data->user_id = $userid;
            $data->charity_id = $request->charity_id;
            $data->amount = $request->amount;
            $data->currency = "GBP";
            $data->ano_donation = $request->ano_donation;
            $data->standing_order = $request->standard ?? "false";
            $data->confirm_donation = $request->c_donation ?? "true";
            $data->charitynote = $request->charitynote;
            $data->mynote = $request->mynote;
            $data->notification = 1;
            $data->status = 0;

            if($data->save()){

                $utransaction = new Usertransaction();
                $utransaction->t_id = time() . "-" . $userid;
                $utransaction->user_id = $userid;
                $utransaction->charity_id = $request->charity_id;
                $utransaction->donation_id = $data->id;
                $utransaction->t_type = "Out";
                $utransaction->amount =  $request->amount;
                $utransaction->title =  "Online Donation";
                $utransaction->status =  1;
                $utransaction->save();

                $user = User::find($userid);
                $user->decrement('balance',$request->amount);
                $user->save();

                $charity = Charity::find($request->charity_id);
                $charity->increment('balance',$request->amount);
                $charity->save();

                $user = User::where('id',$userid)->first();
                $contactmail = ContactMail::where('id', 1)->first()->name;
                $charity = Charity::where('id',$request->charity_id)->first();
                $donation = Donation::where('id',$data->id)->first();

                // card balance update
                if (isset($user->CreditProfileId)) {
                    $CreditProfileId = $user->CreditProfileId;
                    $CreditProfileName = $user->name;
                    $AvailableBalance = 0 - $request->amount;
                    $comment = "Make a donation or Standing order";
                    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                            'CreditProfileId' => $CreditProfileId,
                            'CreditProfileName' => $CreditProfileName,
                            'AvailableBalance' => $AvailableBalance,
                            'comment' => $comment,
                        ]);
                }
                // card balance update end

                $array['name'] = $user->name;
                $array['cc'] = $contactmail;
                $array['client_no'] = $user->accountno;
                $email = $user->email;
                $array['amount'] = $request->amount;
                $array['charity_note'] = $request->charitynote;
                $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

                Mail::to($email)
                ->cc($contactmail)
                ->send(new DonationReport($array));
                
                // return view('frontend.user.donationsuccess', ['success' => 'Donation submited Successfully.', 'userid' => $userid]);
                return view('frontend.user.donationsuccess')->with('success', 'Donation submited Successfully.')->with('userid', $userid);
                // return redirect()->route('onlinedonation.appview')->with('success', 'Donation submited Successfully.');
            }

        }

    }



    public function userDonationStore(Request $request)
    {
        // dd($request->all());
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

        // donor balance
        $userTransactionBalance = Usertransaction::selectRaw('
                SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
            ')
            ->where([
                ['user_id','=', $userid],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=',  $userid],
                ['pending','=', '1']
            ])
            ->first();
        // donor balance end
        $overdrownlimit = User::where('id', $userid)->first()->overdrawn_amount;
        $donorBlanacewithLimit = $userTransactionBalance->balance + $overdrownlimit;

        if ($donorBlanacewithLimit < $request->amount) {
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>You don't have sufficient balance for this donation.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        

        $data = new Donation;
        $data->user_id = $userid;
        $data->charity_id = $request->charity_id;
        $data->amount = $request->amount;
        $data->currency = "GBP";
        $data->ano_donation = $request->ano_donation;
        $data->standing_order = $request->standard;
        $data->confirm_donation = $request->c_donation;
        $data->charitynote = $request->charitynote;
        $data->mynote = $request->mynote;
        $data->notification = 1;
        $data->status = 0;

        if($data->save()){

            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . $userid;
            $utransaction->user_id = $userid;
            $utransaction->charity_id = $request->charity_id;
            $utransaction->donation_id = $data->id;
            $utransaction->t_type = "Out";
            $utransaction->amount =  $request->amount;
            $utransaction->title =  "Online Donation";
            $utransaction->status =  1;
            $utransaction->save();

            $user = User::find($userid);
            $user->decrement('balance',$request->amount);
            $user->save();

            $charity = Charity::find($request->charity_id);
            $charity->increment('balance',$request->amount);
            $charity->save();

            $user = User::where('id',$userid)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $charity = Charity::where('id',$request->charity_id)->first();
            $donation = Donation::where('id',$data->id)->first();


            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $request->amount;
                $comment = "Make a donation or Standing order";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

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
            $array['cc'] = $contactmail;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['amount'] = $request->amount;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationReport($array));

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation submited Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }


    }

    public function userDonationAdminStore(Request $request)
    {


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

        if($request->c_donation == "false"){
            $message ="<div class='alert alert-danger'>Please accept condition.</div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $donner_id = $request->donner_id;

        // donor balance
        $userTransactionBalance = Usertransaction::selectRaw('
                SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
            ')
            ->where([
                ['user_id','=', $donner_id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=',  $donner_id],
                ['pending','=', '1']
            ])
            ->first();
        // donor balance end
        $overdrownlimit = User::where('id', $donner_id)->first()->overdrawn_amount;
        $donorBlanacewithLimit = $userTransactionBalance->balance + $overdrownlimit;

        if ($donorBlanacewithLimit < $request->amount) {
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>You don't have sufficient balance for this donation.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = new Donation;
        $data->user_id = $donner_id;
        $data->charity_id = $request->charity_id;
        $data->amount = $request->amount;
        $data->currency = "GBP";
        $data->ano_donation = $request->ano_donation;
        $data->standing_order = $request->standard;
        $data->confirm_donation = $request->c_donation;
        $data->charitynote = $request->charitynote;
        $data->mynote = $request->mynote;
        $data->notification = 1;
        $data->status = 0;

        if($data->save()){

            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . $donner_id;
            $utransaction->user_id = $donner_id;
            $utransaction->charity_id = $request->charity_id;
            $utransaction->donation_id = $data->id;
            $utransaction->t_type = "Out";
            $utransaction->amount =  $request->amount;
            $utransaction->title =  "Online Donation";
            $utransaction->status =  1;
            $utransaction->save();

            $user = User::find($donner_id);
            $user->decrement('balance',$request->amount);
            $user->save();

            $charity = Charity::find($request->charity_id);
            $charity->increment('balance',$request->amount);
            $charity->save();

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $request->amount;
                $comment = "Online Donation card balance adjust";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            $user = User::where('id',$donner_id)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['name'] = $user->name;
            $array['cc'] = $contactmail;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['amount'] = $request->amount;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationReport($array));

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation submited Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
    }


    public function userstandingDonationAdminStore(Request $request)
    {


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

        $donner_id = $request->donner_id;

        $data = new StandingDonation;
        $data->user_id = $donner_id;
        $data->charity_id = $request->charity_id;
        $data->amount = $request->amount;
        $data->currency = "GBP";
        $data->ano_donation = $request->ano_donation;
        $data->standing_order = $request->standard;
        $data->payments = $request->payments_type;
        $data->number_payments = $request->number_payments;
        $data->starting = $request->starting;
        $data->interval = $request->interval;
        $data->charitynote = $request->charitynote;
        $data->mynote = $request->mynote;
        $data->notification = 1;
        $data->status = 0;

        if($data->save()){

            $user = User::where('id',$donner_id)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $donation = StandingDonation::where('id',$data->id)->first();

            $array['name'] = $user->name;
            $array['cc'] = $contactmail;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['amount'] = $request->amount;
            $array['donation'] = $donation;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationstandingReport($array));

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Standing order donation submited successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function addAccount(Request $request)
    {
        if(empty($request->accno)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill account field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = User::find($request->donnerId);
        $user->accountno = $request->accno;
        $user->status = 1;
        if($user->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Account added successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
    }

    public function updateOverdrawnAmount(Request $request)
    {


        if(empty($request->overdrawnno)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Overdrawn field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = User::find($request->overdrawnid);
        $oldoverdrownamnt = $user->overdrawn_amount;
        $user->overdrawn_amount = $request->overdrawnno;
        if($user->save()){

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = $request->overdrawnno - $oldoverdrownamnt;
                $comment = "add overdrawn amount by admin";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            $record = new OverdrawnRecord;
            $record->user_id = $request->overdrawnid;
            $record->amount = $request->overdrawnno;
            $record->created_by = Auth::user()->name;
            $record->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Overdrawn amount update successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }

    public function updateUserOverdrawnAmount(Request $request)
    {
        $checkadmin = OverdrawnRecord::where('user_id','=', Auth::user()->id)->where('created_by','=','Admin')->count();

        if($checkadmin > 0 ){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Some comment.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->overdrawnno)){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill Overdrawn field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $user = User::find($request->overdrawnid);
        $user->overdrawn_amount = $request->overdrawnno;
        if($user->save()){

            $record = new OverdrawnRecord;
            $record->user_id = $request->overdrawnid;
            $record->amount = $request->overdrawnno;
            $record->created_by = Auth::user()->name;
            $record->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Overdrawn amount update successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function userStandingOrderinAdmin($id)
    {
        $donation = Donation::where([
            ['standing_order','=', 'true'],
            ['user_id','=', $id],
            ['status','!=','3']
        ])->get();

        $pdonation = Donation::where([
            ['standing_order','=', 'true'],
            ['user_id','=', $id],
            ['status','=','3']
        ])->get();

        return view('donor.standingorder')
        ->with('donor_id',$id)
        ->with('donation',$donation)
        ->with('pdonation',$pdonation);
    }

    public function userDonationrecod()
    {
        $donation = Donation::where([
            ['standing_order','=', 'false'],
            ['user_id','=', auth()->user()->id]
        ])->get();

        return view('frontend.user.donationrecord')
        ->with('donation',$donation);
    }

    public function userStandingrecod()
    {
        $donation = StandingDonation::where([
            ['user_id','=', auth()->user()->id]
        ])->get();

        return view('frontend.user.standingdonationrecord')
        ->with('donation',$donation);
    }

    public function userDonationrecodinAdmin($id)
    {
        $donation = Donation::where([
            ['standing_order','=', 'false'],
            ['user_id','=', $id]
        ])->get();

        return view('donor.donationrecord')
        ->with('donation',$donation)
        ->with('donor_id',$id);
    }

    public function donationlist()
    {
        $donation = Donation::where([
            ['standing_order','=', 'false'],
            ['status','=','0']
        ])->get();
        return view('donor.donationlist',compact('donation'));
    }

    public function pendingdonationlist()
    {
        $donation = Donation::where([
            ['status','=','0']
        ])->get();
        return view('donor.donationlist',compact('donation'));
    }

    public function donationRecord()
    {
        $donation = Donation::where([
            ['status','!=','0']
        ])->orderBy('id','DESC')->get();
        return view('donor.record',compact('donation'));
    }


    public function donationStatus(Request $request)
    {
        $user_id = Donation::where('id',$request->did)->first()->user_id;
        $charity_id = Donation::where('id',$request->did)->first()->charity_id;
        $donation = Donation::where('id',$request->did)->first();
        $balance = Donation::where('id',$request->did)->first()->amount;

        if($request->status == "3"){

        $user = User::find($user_id);
        $user->increment('balance',$balance);
        $user->save();

        // card balance update
        if (isset($user->CreditProfileId)) {
            $CreditProfileId = $user->CreditProfileId;
            $CreditProfileName = $user->name;
            $AvailableBalance = $balance;
            $comment = "Donation status";
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                    'CreditProfileId' => $CreditProfileId,
                    'CreditProfileName' => $CreditProfileName,
                    'AvailableBalance' => $AvailableBalance,
                    'comment' => $comment,
                ]);
        }
        // card balance update end

        $charity = Charity::find(Donation::where('id',$request->did)->first()->charity_id);
        $charity->decrement('balance',$balance);
        $charity->save();

        $utran = Usertransaction::find(Usertransaction::where('donation_id',$request->did)->first()->id);
        $utran->status = 0;
        $utran->save();

        }

        if($request->status == "1"){

            $user = User::where('id',$user_id)->first();
            $charity = Charity::where('id',$charity_id)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $pdf = PDF::loadView('invoices.donation_report_charity', compact('user','charity','donation'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'Donation-report-charity#'.$charity->id.'.pdf', $output);
            $array['file'] = public_path().'/invoices/Donation-report-charity#'.$charity->id.'.pdf';
            $array['file_name'] = 'Donation-report-charity#'.$charity->id.'.pdf';
            $array['cc'] = $contactmail;
            $array['charity'] = $charity;
            $array['user'] = $user;
            $email = $charity->email;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationreportCharity($array));

        }

        $order = Donation::find($request->did);
        $order->status = $request->status;
        if($order->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
    }

    // stripe
    public function stripeDonation()
    {
        return view('frontend.user.strip_topup');
    }

    // all donor email send
    public function addDonorMail()
    {
        return view('donor.custom_mail');
    }

    // all donor mail send
    public function donorMailSend(Request $request)
    {

        $validatedData = $request->validate([
            'subject' => 'required',
            'body' => 'required',
        ]);

        $users = User::where('is_type', 'user')->get();
        $contactmail = ContactMail::where('id', 1)->first()->name;

        foreach ($users as $key => $user) {
            $array['cc'] = $contactmail;
            $array['view'] = 'mail.donorMail';
            $array['name'] = $user->name;
            $array['email'] = $user->email;
            $array['phone'] = $user->phone;
            $array['subject'] = $request->subject;
            $array['body'] = $request->body;
            $array['from'] = 'info@tevini.co.uk';
            
            Mail::to($user->email)->cc($contactmail)->queue(new DonorCustomMail($array));
        }
        return redirect()->back()->with('success', 'Mail send successfully.');
    }

    // donor email send
    public function sendemail($id)
    {
        $user = User::where('id','=', $id)->first();
        return view('donor.sendemail')
        ->with('user',$user);
    }


    public function mailsend(Request $request)
    {


        $validatedData = $request->validate([
            'subject' => 'required',
            'emailto' => 'required|email',
            'body' => 'required',
        ]);


            $user = User::where('id',$request->userid)->first();

            $contactmail = ContactMail::where('id', 1)->first()->name;

            $array['cc'] = $contactmail;
            $array['name'] = $user->name;
            $array['email'] = $user->email;
            $array['phone'] = $user->phone;
            $email = $request->emailto;
            $array['subject'] = $request->subject;
            $array['body'] = $request->body;
            $array['from'] = 'info@tevini.co.uk';

            Mail::send('mail.donorMail', compact('array'), function($message)use($array,$email) {
                $message->from($array['from'], 'Tevini.co.uk');
                $message->to($email)->cc($array['cc'])->subject($array['subject']);
               });

            return redirect()->back()->with('success', 'Mail send successfully.');
    }


}
