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
use Yajra\DataTables\Facades\DataTables;
use PDF;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\DTOs\OnlineDonationData;
use App\Exceptions\Donation\DonationException;
use App\Services\Donation\OnlineDonationService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class DonorController extends Controller
{
    public function index()
    {
        return view('donor.index');
    }

    public function addDonor2()
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

    public function addDonor()
    {
        return view('donor.adddonor');
    }

    public function donorData()
    {
        $users = User::where('is_type','user')
            ->select('id','name','surname','email','phone','accountno','town','balance','overdrawn_amount','email_verified_at')
            ->withSum(['pendingVouchers as pending_out' => function ($q) {
                $q->where('waiting', 'No')
                ->where('status', '0')
                ->where(function ($q2) {
                    $q2->where('expired', '!=', 'Yes')
                        ->orWhereNull('expired');
                });
            }], 'amount');

        return datatables()->eloquent($users)
            ->addColumn('balance', function($u){
                $value = $u->getLiveBalance();
                return '£' . number_format($value, 2);
            })
            ->addColumn('overdrawn_amount', function($u){
                return '£' . number_format($u->overdrawn_amount, 2). '<a overdrawn-id="'.$u->id.'" class="overdrawn" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                <i class="fa fa-edit" style="color: #2094f3; font-size: 16px;"></i>
                                            </a>';
            })
            ->addColumn('pending', function ($u) {
                return '£' . number_format($u->pending_out ?? 0, 2);
            })
            ->addColumn('fullname', function($u){
                return $u->name.' '.$u->surname.
                    ' <i class="fa fa-'.($u->email_verified_at ? 'check-circle' : 'times-circle').'" 
                        style="color:'.($u->email_verified_at ? 'green' : 'red').'"></i>';
            })
            ->filterColumn('fullname', function($query, $keyword) {
                $query->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ["%{$keyword}%"]);
            })
            
            ->addColumn('action', function($u){
                return view('donor.partials.action', compact('u'))->render();
            })
            ->rawColumns(['overdrawn_amount', 'fullname','action'])
            ->make(true);
    }


    public function donorStore(Request $request)
    {
        // ✅ FIX: Comprehensive validation with proper rules
        $this->validate($request, [
            'prefix_name' => 'nullable|string|max:191',
            'fname' => 'required|string|max:255',
            'surname' => 'required|string|max:50',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:256',
            'street' => 'nullable|string|max:256',
            'postcode' => 'nullable|string|max:50',
            'houseno' => 'nullable|string|max:256',
            'town' => 'nullable|string|max:50',
            'accno' => 'nullable|string|max:50',
            'balance' => 'nullable|numeric'
        ]);

        try {
            DB::beginTransaction();

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
            $user->is_type = 'user'; // ✅ Ensure is_type is set
            
            if($request->accno){
                $user->accountno = $request->accno;
                $user->status = "1";
            } else {
                $user->status = "0"; // Default status
            }
            
            $user->balance = $request->filled('balance') ? $request->balance : 0.00;
            $user->password = Hash::make('123456');
            
            $user->save();

            DB::commit();

            return back()->with('message', 'Donor Created Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            // ✅ FIX: Log the error to find exact issue
            Log::error('Admin Donor Store Failed: ' . $e->getMessage());
            return back()->with('error', 'Server Error!! Could not create donor.')->withInput();
        }
    }

    public function donorEdit($id)
    {

        $users = User::where('id','=' ,decrypt($id))->first();
        return view('donor.editdonor', compact('users'));

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
        $user->ppv_account = $request->ppv_account;
        $user->standard_amount = $request->standard_amount;

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
        // ✅ FIX: Check if donor has any transactions before deleting
        $hasTransactions = Usertransaction::where('user_id', $request->donorId)->exists();

        if ($hasTransactions) {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Cannot delete donor. This donor has existing transactions.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // ✅ Check if donor has any standing orders
        $hasStandingOrders = StandingDonation::where('user_id', $request->donorId)->exists();
        if ($hasStandingOrders) {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Cannot delete donor. This donor has existing standing orders.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // If no transactions or standing orders, proceed to delete
        if (User::destroy($request->donorId)) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donor deleted successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        } else {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Delete Fail...</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }


    public function topup($id,$amount)
    {
        $topup = User::where('id','=', $id)->first();

        $donateBy = Usertransaction::select('donation_by')->whereNotNull('donation_by')->groupBy('donation_by')->get();

        return view('donor.topup')
                ->with('topup',$topup)
                ->with('donateBy',$donateBy)
                ->with('amount',$amount);
    }

    public function topupStore2(Request $request)
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

            if ($request->commission != '0') {
                $topup = new Commission();
                $topup->user_id = $request->topupid;
                $topup->commission = $request->commission;
                $topup->save();
            }
            $transaction = new Transaction();
            $transaction->t_id = uniqid() . "-" . $request->topupid;
            $transaction->user_id = $request->topupid;
            $transaction->t_type = "In";
            $transaction->name = $request->source;
            $transaction->commission = $request->commission;
            $transaction->amount = $request->gbalance;
            $transaction->note = $request->note;
            $transaction->status = "1";
            $transaction->save();

            $utransaction = new Usertransaction();
            $utransaction->t_id = uniqid() . "-" . $request->topupid;
            $utransaction->date = $request->date;
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
                $expgiftaidamnt = $request->gbalance * 25/100;
                $donorgiftaid = User::find($request->topupid);
                $donorgiftaid->expected_gift_aid = $donorgiftaid->expected_gift_aid + $expgiftaidamnt;

                if ($donorgiftaid->gift_aid_currenction > 0) {
                    $donorgiftaid->gift_aid_currenction = $donorgiftaid->gift_aid_currenction + $expgiftaidamnt;
                }

                if ($donorgiftaid->current_yr_gift_aid > 0) {
                    $donorgiftaid->current_yr_gift_aid = $donorgiftaid->current_yr_gift_aid + $expgiftaidamnt;
                }


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
                $cleargiftaid->expected_gift_aid = $cleargiftaid->expected_gift_aid - $request->gbalance;
                $cleargiftaid->save();

                
                $utransaction->clear_gift = 1;
                $utransaction->save();
            }

            if($request->receipt == 'true'){
                $user = User::where('id',$request->topupid)->first();

                $contactmail = ContactMail::where('id', 1)->first()->name;

                $balance = $request->gbalance;
                $source = $request->source;
                $donationDate = $request->date;
                $title = "DONATION RECEIPT";
                $pdf = PDF::loadView('invoices.topup_report', compact('balance','source','user','donationBy','title','utransaction','donationDate'));
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
                $array['date'] = $request->date;
                $array['transaction'] = $utransaction;

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


    public function topupStore(Request $request)
    {
        if (empty($request->gbalance)) {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill amount field.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // Use Laravel's boolean method for safer true/false checks
        $isGift = $request->boolean('gift');
        $isClearGift = $request->boolean('cleargift');
        $wantsReceipt = $request->boolean('receipt');

        // Sanitize amounts
        $topupAmount = (float) $request->gbalance;
        $finalBalance = (float) $request->balance;
        $commissionAmount = (float) $request->commission;

        // Generate the new Transaction ID (e.g., TP-1715683405-15)
        $transactionId = 'TP-' . uniqid() . '-' . $request->topupid;

        // Start Database Transaction to ensure data integrity
        \DB::beginTransaction();

        try {
            $user = User::find($request->topupid);
            $user->balance = $user->balance + $finalBalance;
            $user->save();

            if ($commissionAmount > 0) {
                $topup = new Commission();
                $topup->user_id = $request->topupid;
                $topup->commission = $commissionAmount;
                $topup->save();
            }

            $transaction = new Transaction();
            $transaction->t_id = $transactionId; // Updated format
            $transaction->user_id = $request->topupid;
            $transaction->t_type = "In";
            $transaction->name = $request->source;
            $transaction->commission = $commissionAmount;
            $transaction->amount = $topupAmount;
            $transaction->note = $request->note;
            $transaction->status = "1";
            $transaction->save();

            $utransaction = new Usertransaction();
            $utransaction->t_id = $transactionId; // Updated format
            $utransaction->date = $request->date;
            $utransaction->user_id = $request->topupid;
            $utransaction->source = $request->source;
            $utransaction->t_type = "In";
            $utransaction->commission = $commissionAmount;
            $utransaction->amount = $finalBalance;
            $utransaction->note = $request->note;
            $utransaction->donation_by = $request->donationBy;
            $utransaction->gift = $isGift ? 1 : 0;
            $utransaction->title = 'Credit';
            $utransaction->status = 1;
            $utransaction->save();

            if ($isGift) {
                $expgiftaidamnt = $topupAmount * 25 / 100;
                
                // Reuse the already loaded $user model instead of querying again
                $user->expected_gift_aid = $user->expected_gift_aid + $expgiftaidamnt;

                if ($user->gift_aid_currenction > 0) {
                    $user->gift_aid_currenction = $user->gift_aid_currenction + $expgiftaidamnt;
                }

                if ($user->current_yr_gift_aid > 0) {
                    $user->current_yr_gift_aid = $user->current_yr_gift_aid + $expgiftaidamnt;
                }

                $user->save();

                $expgiftaidtran = new ExpectedGiftAid();
                $expgiftaidtran->user_id = $request->topupid;
                $expgiftaidtran->transaction_id = $transaction->id;
                $expgiftaidtran->usertransaction_id = $utransaction->id;
                $expgiftaidtran->amount = $finalBalance;
                $expgiftaidtran->gift_amount = $expgiftaidamnt;
                $expgiftaidtran->save();
            }

            if ($isClearGift) {
                $user->expected_gift_aid = $user->expected_gift_aid - $topupAmount;
                $user->save();

                $utransaction->clear_gift = 1;
                $utransaction->save();
            }

            // Commit to database if everything worked
            \DB::commit();

        } catch (\Exception $e) {
            // Rollback if any database query fails
            \DB::rollBack();
            
            // Log the error: \Log::error($e->getMessage());
            
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>A database error occurred. Transaction failed.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        // Handle PDF & Email (Outside DB Transaction to prevent timeouts)
        if ($wantsReceipt) {
            try {
                $contactmail = ContactMail::where('id', 1)->value('name');

                $balance = $finalBalance;
                $gbalance = $topupAmount;
                $commission = $commissionAmount;
                $source = $request->source;
                $donationBy = $request->donationBy;
                $donationDate = $request->date;
                $title = "DONATION RECEIPT";
                
                $pdf = PDF::loadView('invoices.topup_report', compact('balance','source','user','donationBy','title','utransaction','donationDate','gbalance','commission'));
                $output = $pdf->output();
                
                // Ensure directory exists
                $invoiceDir = public_path('invoices');
                if (!file_exists($invoiceDir)) {
                    mkdir($invoiceDir, 0755, true);
                }
                
                $fileName = 'Donation-report#' . $user->id . '.pdf';
                $filePath = $invoiceDir . '/' . $fileName;
                file_put_contents($filePath, $output);

                $array['file'] = $filePath;
                $array['file_name'] = $fileName;
                $array['cc'] = $contactmail;
                $array['name'] = $user->name;
                $array['email'] = $user->email;
                $array['phone'] = $user->phone;
                $array['balance'] = $finalBalance;
                $array['gbalance'] = $topupAmount;
                $array['commission'] = $commissionAmount;
                $array['source'] = $request->source;
                $array['date'] = $request->date;
                $array['transaction'] = $utransaction;

                Mail::to($user->email)
                    ->cc($contactmail)
                    ->send(new TopupReport($array));

                $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully & receipt sent to donor mail.</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
                
            } catch (\Exception $e) {
                // If DB saved but email fails, still inform the user the balance was saved
                // Log::error('Mail Error: ' . $e->getMessage());
                $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully, but email receipt failed to send.</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            }
        }

        $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance added successfully.</b></div>";
        return response()->json(['status' => 300, 'message' => $message]);
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


    // M statement in admin
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
        
        // Fetch User Record
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 303, 'message' => 'User not found.']);
        }

        // ✅ FIX: Get balance directly from User Model
        $userBalance = $user->getLiveBalance();

        $tamount = Usertransaction::where('user_id','=', $id)
            ->where('status','=', '1')
            ->orderBy('id','DESC')
            ->get();

        if(!empty($request->input('fromdate')) && !empty($request->input('todate'))){
            $fromDate = $request->input('fromdate');
            $toDate   = $request->input('todate');
            
            // ✅ FIX: Grouped where conditions to prevent OR condition from ignoring user_id
            $report = Usertransaction::where('user_id', $id)
                ->where(function($query) use ($fromDate, $toDate) {
                    $query->where([
                        ['created_at', '>=', $fromDate],
                        ['created_at', '<=', $toDate.' 23:59:59'],
                        ['status','=', '1']
                    ])
                    ->orWhere([
                        ['pending','=', '0']
                    ]);
                })
                ->orderBy('id','DESC')
                ->get();

        } else {
            // ✅ FIX: Grouped where conditions
            $report = Usertransaction::where('user_id', $id)
                ->where(function($query) {
                    $query->where('status', '1')
                          ->orWhere('pending', '0');
                })
                ->orderBy('id','DESC')
                ->get();

            $fromDate = "";
            $toDate   = "";
        }

        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';
        $array['cc'] = $contactmail;
        
        $pdf = PDF::loadView('invoices.donor_report', compact('report','fromDate','toDate','user','tamount'));
        $output = $pdf->output();
        $pdfPath = public_path().'/invoices/Report#'.$id.'.pdf';
        file_put_contents($pdfPath, $output);
        
        $array['name'] = $user->name;
        // ✅ FIX: Use model balance variable
        $array['userbalance'] =  number_format($userBalance, 2);
        $array['view'] = 'mail.donorreport';
        $array['subject'] = 'Monthly statement';
        $array['from'] = 'info@tevini.co.uk';
        $array['content'] = 'Hi, Your donation report has been placed';
        $array['file'] = $pdfPath;
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

        $contactmail = ContactMail::where('id', 1)->value('name') ?? 'info@tevini.co.uk';

        $processUser = function ($user) use ($fromDate, $toDate, $contactmail) {
            $tamount = Usertransaction::where('user_id', $user->id)
                ->where('status', 1)
                ->orderBy('id', 'DESC')
                ->get();

            // ✅ FIX: Grouped where conditions to prevent OR condition from ignoring user_id
            $report = Usertransaction::where('user_id', $user->id)
                ->where(function ($query) use ($fromDate, $toDate) {
                    $query->where(function($q) use ($fromDate, $toDate) {
                        $q->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59'])
                          ->where('status', 1);
                    })->orWhere('pending', 0);
                })
                ->orderBy('id', 'DESC')
                ->get();

            // ✅ FIX: Get balance directly from User Model
            $userBalance = $user->getLiveBalance();

            $pdf = PDF::loadView('invoices.donor_report', compact('report', 'fromDate', 'toDate', 'user', 'tamount'));
            $filePath = public_path('/invoices/Report#' . $user->id . '.pdf');
            file_put_contents($filePath, $pdf->output());

            $array = [
                'cc' => $contactmail,
                // ✅ FIX: Use model balance variable
                'userbalance' => number_format($userBalance, 2),
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
        return view('frontend.user.donation');
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
        }

        // ✅ FIX: Validate numeric, minimum amount, and max 2 decimal places
        if(!is_numeric($request->tdfamount) || $request->tdfamount < 18){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Minimum transfer is £18.00.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }
        
        // ✅ FIX: Use Regex for exact 2 decimal places validation
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $request->tdfamount)) {
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Invalid amount format.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }

        // ✅ FIX: Prepare email data OUTSIDE the transaction
        $user = User::find($donor_id);
        if (!$user) {
            return response()->json(['status'=> 303,'message'=>'Donor not found.']);
        }
        $userName = $user->name;
        $userEmail = $user->email;
        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';

        try {
            // ✅ FIX: Wrap entire operation in DB transaction with row locking
            DB::transaction(function () use ($request, $donor_id) {
                
                // ✅ FIX: Lock donor row for concurrent safety
                $user = User::where('id', $donor_id)->lockForUpdate()->first();
                
                // ✅ FIX: Use User model's getAvailableLimit() method for accurate balance
                $donorBlanacewithLimit = $user->getAvailableLimit();

                if($request->tdfamount > $donorBlanacewithLimit){
                    throw new \Exception("You don't have enough balance to transfer.");
                }
                
                // Save TDF record
                $data = new TdfTransaction();
                $data->issue_date = date('Y-m-d');
                $data->user_id = $donor_id;
                $data->tdfaccount = $request->tdfaccount;
                $data->tdf_amount = $request->tdfamount;
                $data->current_dollar_amount = $request->tdfamount;
                $data->save();

                // Decrement balance
                $user->balance = $user->balance - $request->tdfamount;
                $user->save();

                // Save Usertransaction
                $udtransaction = new Usertransaction();
                $udtransaction->t_id =  "TDF-" . uniqid() . "-" . $donor_id;
                $udtransaction->user_id = $donor_id;
                $udtransaction->t_type = "Out";
                $udtransaction->amount =  $request->tdfamount;
                $udtransaction->t_unq = uniqid().rand(1,100);
                $udtransaction->title ="Transfer to TDF";
                $udtransaction->status =  1;
                $udtransaction->save();

                // Save Transaction
                $transaction = new Transaction();
                $transaction->t_id =  "TDF-" . uniqid() . "-" . $donor_id;
                $transaction->user_id = $donor_id;
                $transaction->t_type = "Out";
                $transaction->amount =  $request->tdfamount;
                $transaction->note ="Transfer to TDF";
                $transaction->status =  1;
                $transaction->save();
            });

            // ✅ FIX: Email sent completely outside transaction scope
            try {
                $array['name'] = $userName;
                $array['subject'] = 'TDF Transfer';
                $array['from'] = 'info@tevini.co.uk';
                $array['cc'] = $contactmail;
                
                Mail::to($userEmail)
                    ->cc($contactmail)
                    ->send(new TDFTransfer($array));
            } catch (\Exception $e) {
                Log::error('Admin TDF Transfer email failed for user ' . $donor_id . ': ' . $e->getMessage());
            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Successfully transferred to TDF.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);

        } catch (\Exception $e) {
            Log::error('Admin TDF Transfer failed for user ' . $donor_id . ': ' . $e->getMessage());
            
            $errorMessage = $e->getMessage() === "You don't have enough balance to transfer." 
                ? $e->getMessage() 
                : "Server error!!";

            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>".$errorMessage."</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }
    }

    public function userDonationAdmin($id)
    {
        $donor_id = $id;
        return view('donor.onlinedonation',compact('donor_id'));
    }



    // ================================================================
    // App: One-Time OR Standing Donation
    // ================================================================
    public function userOnlineDonationStore(Request $request)
    {
        try {
            $isStanding = ($request->standard == "on" || $request->standard == "true");

            $data = OnlineDonationData::fromDonorApp(
                donorId:        (int) $request->userid,
                charityId:      (int) $request->charity_id,
                amount:         (float) $request->amount,
                isAnonymous:    $request->boolean('ano_donation'),
                isStandingOrder: $isStanding,
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: $request->has('confirm_donation') || $request->has('c_donation'),
                paymentsType:    $request->payments_type,
                numberPayments:  $request->number_payments ? (int) $request->number_payments : null,
                startingDate:    $request->starting,
                intervalValue:   $request->interval ? (int) $request->interval : null,
            );

            $service = app(OnlineDonationService::class);

            if ($isStanding) {
                $service->createStandingDonation($data);
                $msg = 'Standing order donation submitted successfully.';
            } else {
                $service->createOneTimeDonation($data);
                $msg = 'Donation submitted successfully.';
            }

            return view('frontend.user.donationsuccess')
                ->with('success', $msg)
                ->with('userid', $request->userid);

        } catch (DonationException $e) {
            return back()->with('error', $e->getUserMessage());
        } catch (\Throwable $e) {
            \Log::error('App donation failed: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // ================================================================
    // One-Time Donation
    // ================================================================
    public function userDonationAdminStore(Request $request)
    {
        try {
            $data = OnlineDonationData::fromAdminOneTime(
                donorId:        (int) $request->donner_id,
                adminId:        Auth::id(),
                charityId:      (int) $request->charity_id,
                amount:         (float) $request->amount,
                isAnonymous:    $request->boolean('ano_donation'),
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: $request->c_donation !== 'false',
            );

            $donation = app(OnlineDonationService::class)->createOneTimeDonation($data);

            $message = "<div class='alert alert-success'><b>Donation submitted successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } catch (DonationException $e) {
            $message = "<div class='alert alert-danger'><b>{$e->getUserMessage()}</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        } catch (\Throwable $e) {
            \Log::error('Admin donation failed: ' . $e->getMessage());
            return response()->json([
                'status'  => 303,
                'message' => "<div class='alert alert-danger'><b>Something went wrong.</b></div>"
            ]);
        }
    }

    // ================================================================
    // Standing Order Donation
    // ================================================================
    public function userstandingDonationAdminStore(Request $request)
    {
        try {
            $data = OnlineDonationData::fromAdminStanding(
                donorId:        (int) $request->donner_id,
                adminId:        Auth::id(),
                charityId:      (int) $request->charity_id,
                amount:         (float) $request->amount,
                isAnonymous:    $request->boolean('ano_donation'),
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: $request->c_donation !== 'false',
                paymentsType:    $request->payments_type,
                numberPayments:  $request->number_payments ? (int) $request->number_payments : null,
                startingDate:    $request->starting,
                intervalValue:   (int) $request->interval,
            );

            $standing = app(OnlineDonationService::class)->createStandingDonation($data);

            $message = "<div class='alert alert-success'><b>Standing order donation submitted successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } catch (DonationException $e) {
            $message = "<div class='alert alert-danger'><b>{$e->getUserMessage()}</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        } catch (\Throwable $e) {
            \Log::error('Admin standing donation failed: ' . $e->getMessage());
            return response()->json([
                'status'  => 303,
                'message' => "<div class='alert alert-danger'><b>Something went wrong.</b></div>"
            ]);
        }
    }

    // ================================================================
    // Update Standing Order
    // ================================================================
    public function userstandingDonationAdminUpdate(Request $request)
    {
        try {
            $data = new OnlineDonationData(
                donorId:        0,
                orderedByUserId: Auth::id(),
                source:         'admin',
                charityId:      0,
                amount:         (float) $request->amount,
                isAnonymous:    false,
                isStandingOrder: true,
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: true,
                paymentsType:    $request->payments,
                numberPayments:  $request->number_payments ? (int) $request->number_payments : null,
                startingDate:    $request->starting,
                intervalValue:   (int) $request->interval,
            );

            $standing = app(OnlineDonationService::class)->updateStandingDonation($request->donation_id, $data);

            $message = "<div class='alert alert-success'><b>Standing order updated successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } catch (\Throwable $e) {
            $message = "<div class='alert alert-danger'><b>Failed to update standing order.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }
    }


    // ================================================================
    // One-Time Donation
    // ================================================================
    public function userDonationStore(Request $request)
    {
        try {
            $data = OnlineDonationData::fromDonorWebOneTime(
                donorId:        (int) $request->userid,
                charityId:      (int) $request->charity_id,
                amount:         (float) $request->amount,
                isAnonymous:    $request->boolean('ano_donation'),
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: $request->c_donation !== 'false',
            );

            $donation = app(OnlineDonationService::class)->createOneTimeDonation($data);

            $message = "<div class='alert alert-success'><b>Donation submitted successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } catch (DonationException $e) {
            $message = "<div class='alert alert-danger'><b>{$e->getUserMessage()}</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 303,
                'message' => "<div class='alert alert-danger'><b>Something went wrong.</b></div>"
            ]);
        }
    }

    // ================================================================
    // Standing Order Donation
    // ================================================================
    public function userStantingDonationStore(Request $request)
    {
        try {
            $data = OnlineDonationData::fromDonorWebStanding(
                donorId:        (int) $request->userid,
                charityId:      (int) $request->charity_id,
                amount:         (float) $request->amount,
                isAnonymous:    $request->boolean('ano_donation'),
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: $request->c_donation !== 'false',
                paymentsType:    $request->payments_type,
                numberPayments:  $request->number_payments ? (int) $request->number_payments : null,
                startingDate:    $request->starting,
                intervalValue:   (int) $request->interval,
            );

            $standing = app(OnlineDonationService::class)->createStandingDonation($data);

            $message = "<div class='alert alert-success'><b>Standing order donation submitted successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } catch (DonationException $e) {
            $message = "<div class='alert alert-danger'><b>{$e->getUserMessage()}</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 303,
                'message' => "<div class='alert alert-danger'><b>Something went wrong.</b></div>"
            ]);
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

        $pdonation = StandingDonation::with('standingdonationDetail')->where([
            ['user_id','=', $id]
        ])->where('status', 0)->get();

        $donation = StandingDonation::with('standingdonationDetail')->where([
            ['user_id','=', $id]
        ])->where('status', 1)->get();


        return view('donor.standingorder')
                ->with('donor_id',$id)
                ->with('donation',$donation)
                ->with('pdonation',$pdonation);
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
        ])
        ->latest() 
        ->get();
        $charities = $donation->pluck('charity')->unique('id')->values();
        return view('donor.donationlist',compact('donation','charities'));
    }

    public function pendingdonationlist()
    {
        $donation = Donation::where([
            ['status','=','0']
        ])->get();
        $charities = $donation->pluck('charity')->unique('id')->values();
        return view('donor.donationlist',compact('donation','charities'));
    }

    public function donationRecord(Request $request)
    {
        if ($request->ajax()) {

            $query = Donation::with(['user', 'charity'])
                    ->where('status', '!=', 0)
                    ->orderBy('id', 'DESC');

            return DataTables::of($query)
                ->addColumn('donor', fn($d) => $d->user->name.' '.$d->user->surname)
                ->addColumn('beneficiary', fn($d) => $d->charity->name)
                ->addColumn('anonymous', fn($d) => $d->ano_donation == "true" ? 'Yes' : 'No')
                ->addColumn('standing', fn($d) => $d->standing_order == "true" ? 'Yes' : 'No')
                ->addColumn('date', fn($d) => $d->created_at->format('d/m/Y'))
                ->addColumn('status_label', function ($d) {
                    if ($d->status == 1) {
                        return '<button class="btn btn-sm btn-success">Confirm</button>';
                    } elseif ($d->status == 3) {
                        return '<button class="btn btn-sm btn-danger">Cancel</button>';
                    }
                })
                ->rawColumns(['status_label'])
                ->make(true);
        }

        return view('donor.record');
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

            if ($request->send_email == "1") {
                Mail::to($email)
                ->cc($contactmail)
                ->send(new DonationreportCharity($array));
            }

        }

        $order = Donation::find($request->did);
        $order->status = $request->status;
        if($order->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
    }


    /**
     * Mark selected donations as complete and send emails.
     */
    public function donationComplete(Request $request)
    {
        // 1. Validate incoming request
        $request->validate([
            'donation_ids'   => 'required|array',
            'donation_ids.*' => 'required|integer|exists:donations,id',
            'charity_ids'    => 'required|array',
            'charity_ids.*'  => 'required|integer|exists:charities,id',
            'send_email'     => 'required|in:1,2'
        ]);

        $donationIds = $request->donation_ids;
        $charityIds  = $request->charity_ids;
        $sendEmail   = $request->send_email == "1";

        try {
            // 2. Bulk update donation statuses (Much faster than looping)
            Donation::whereIn('id', $donationIds)->update(['status' => 1]);

            // 3. Get base contact email safely
            $contactmail = ContactMail::find(1)?->name;

            // 4. Process emails and PDFs per charity
            foreach ($charityIds as $cid) {
                
                $charity = Charity::find($cid);
                if (!$charity) {
                    continue; // Skip if charity doesn't exist
                }

                $donations = Donation::whereIn('id', $donationIds)
                    ->where('charity_id', $cid)
                    ->get();

                if ($donations->isEmpty()) {
                    continue;
                }

                // Generate PDF
                $pdf = PDF::loadView('invoices.donations_report_charity', compact('charity', 'donations'));
                
                // Added timestamp to prevent file overwriting conflicts
                $fileName = 'Donation-report-charity-' . $charity->id . '-' . uniqid() . '.pdf';
                $filePath = public_path('invoices/' . $fileName);
                
                file_put_contents($filePath, $pdf->output());

                $data = [
                    'file'      => $filePath,
                    'file_name' => $fileName,
                    'cc'        => $contactmail,
                    'charity'   => $charity,
                ];

                // Send Emails if requested
                if ($sendEmail) {
                    if ($charity->email) {
                        Mail::to($charity->email)->send(new DonationreportCharity($data));
                    }
                    if ($contactmail) {
                        Mail::to($contactmail)->send(new DonationreportCharity($data));
                    }
                }
            }

            $message = "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Donation status changed successfully.</b>";
            
            return response()->json([
                'status'  => 300,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Donation Complete Error: ' . $e->getMessage());

            $errorMessage = "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Something went wrong. Please try again.</b>";

            return response()->json([
                'status'  => 500,
                'message' => $errorMessage
            ]);
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

    public function updateGiftAid(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'gift_aid_currenction' => $request->gift_aid_currenction,
            'current_yr_gift_aid'  => $request->current_yr_gift_aid,
            'prev_yr_gift_aid'     => $request->prev_yr_gift_aid,
        ]);

        return redirect()->back()->with('message', 'Gift Aid fields updated successfully.');
    }


}
