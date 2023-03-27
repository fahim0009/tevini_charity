<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Commission;
use App\Models\User;
use App\Models\Charity;
use Illuminate\Http\Request;

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

    public function charity_login_show()
    {
        return view('frontend.charity.charity_login');
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


    public function charityDashboard()
    {

        return view('charity.dashboard');
    }

  



}
