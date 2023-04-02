<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Commission;
use App\Models\User;
use App\Models\Charity;
use Illuminate\Http\Request;
use App\Models\Usertransaction;
use Illuminate\support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Hash;


class CharityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Charity::orderBy('id','DESC')->get();
        return view('charity.index', compact('users'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email',
            'acc' => 'required',
            ]);

        $user = new Charity;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->address = $request->address;
        $user->town = $request->town;
        $user->post_code = $request->post_code;
        $user->acc_no = $request->acc;
        if($request->balance == "") {
            $user->balance =0;
          }else{
            $user->balance = $request->balance;
          }
        if($user->save()){

            $message ="Charity Created Successfully";
            return back()->with('message', $message);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function show(Charity $charity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $users = Charity::where('id','=' ,decrypt($id))->first();
        return view('charity.editcharity', compact('users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email',
            'number' => 'required',
            'address' => 'required',
            'acc_no' => 'required'
            ]);

        $user = Charity::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->number = $request->number;
        $user->address = $request->address;
        $user->town = $request->town;
        $user->post_code = $request->post_code;
        $user->acc_no = $request->acc_no;
        if($user->save()){

            $message ="Charity Update Successfully";

        return redirect()->route('charitylist')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Charity  $charity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Charity $charity)
    {
        //
    }

    public function deleteCharity($id)
    {
        if( Charity::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Charity has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }


    public function topup($id)
    {
        $topup = Charity::where('id','=', $id)->first();
        return view('charity.topup',compact('topup'));
    }

    public function topupStore(Request $request)
    {
        $user = Charity::find($request->topupid);
        $user->balance = $user->balance + $request->balance;
        if($user->save()){
            $message ="Amount added Successfully";
            return back()->with('message', $message);
        }

        return back();
    }

    public function pay($id)
    {
        $charity = Charity::where('id','=', $id)->first();
        return view('charity.pay',compact('charity'));
    }

    public function payStore(Request $request)
    {
        $user = Charity::find($request->topupid);
        $user->decrement('balance',$request->balance);
        $t_id = time() . "-" . $request->topupid;

            $transaction = new Transaction();
            $transaction->t_id = $t_id;
            $transaction->charity_id = $request->topupid;
            $transaction->t_type = "Out";
            $transaction->name = $request->source;
            $transaction->amount = $request->balance;
            $transaction->note = $request->note;
            $transaction->status = "1";
            $transaction->save();

            $message ="Amount pay Successfully. Transaction id is: ". $t_id;
            return back()->with('message', $message);


        return back();
    }


    // charity function start 

    public function charity_login_show()
    {
        return view('frontend.charity.charity_login');
    }

    public function charityDashboard()
    {

        return view('frontend.charity.dashboard');
    }

    public function profileShow()
    {
        return view('frontend.charity.profile');
    }


    public function updateCharity_profile(Request $request)
    {

        // $chkemail = Charity::where('email','=', $request->email)->whereNotIn('id', [auth('charity')->user()->id])->count();

        // if( $chkemail > 0){
        //     $message ="This email has already exists.";
        //     return redirect()->route('user.profile')->with(['status'=> 303,'error'=> $message]);

        // }

        if ($request->password) {
            if ($request->password != $request->cpassword) {
                $message ="Password doesn't match!!";
                return redirect()->route('charity.profile')->with(['status'=> 303,'error'=> $message]);
            }
        }



        $charity = Charity::findOrFail(auth('charity')->user()->id);
        $charity->name = $request->name;
        $charity->number = $request->phone;
        $charity->address = $request->address;
        $charity->town = $request->town;
        $charity->post_code = $request->postcode;

   

            // if ($request->image) {

            //     $request->validate([
            //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     ]);
            //     $rand = mt_rand(100000, 999999);
            //     $imageName = time(). $rand .'.'.$request->image->extension();
            //     $request->image->move(public_path('images'), $imageName);
            //     $userdata->photo= $imageName;
            // }

        if ($request->password) {
            $charity->password= Hash::make($request->password);
        }

        if ($charity->save()) {
            $message ="Profile Update Successfully";

        return redirect()->route('charity.profile')->with(['status'=> 303,'message'=> $message]);
        }
        else{
            return back()->with(['status'=> 303,'message'=>'Server Error!!']);
        }

    }

    
    public function charityTransaction(Request $request)
    {

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $intransactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions = Transaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['pending','=', '0']
            ])->orderBy('id','DESC')->get();

        }else{

            $intransactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions= Transaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $pending_transactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', auth('charity')->user()->id],
                ['pending','=', '0']
            ])->orderBy('id','DESC')->get();


        }

        return view('frontend.charity.transaction')
        ->with('intransactions',$intransactions)
        ->with('outtransactions',$outtransactions)
        ->with('pending_transactions',$pending_transactions);

    }

  



}
