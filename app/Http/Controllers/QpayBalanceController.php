<?php

namespace App\Http\Controllers;

use App\Models\QpayBalance;
use Illuminate\Http\Request;

class QpayBalanceController extends Controller
{
    public function index()
    {
        $data = QpayBalance::all();
        return view('qpay.index', compact('data'));
    }

    public function edit($id)
    {
        $data = QpayBalance::where('id', $id)->first();
        return view('qpay.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = QpayBalance::findOrFail($id);
        $data->balance = $request->balance;
        if($data->save()){

            $message ="Balance Update Successfully";

        return redirect()->route('qpaybalance')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }

    public function add()
    {
        $data = QpayBalance::all();
        return view('qpay.add', compact('data'));
    }

    public function store(Request $request)
    {
        
        $id = QpayBalance::where('id', 1)->first()->id;
        $data = QpayBalance::findOrFail($id);
        $data->balance = $data->balance + $request->balance;
        if($data->save()){

            $message ="Balance Update Successfully";

        return redirect()->route('qpaybalance')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }
}
