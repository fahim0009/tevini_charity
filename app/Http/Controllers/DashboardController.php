<?php


namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\OrderHistory;
use App\Models\Commission;
use App\Models\User;
use App\Models\Donation;
use App\Models\Provoucher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $donation = Donation::where('status','=','1')->sum('amount');
        $transaction = Transaction::whereNotNull('charity_id')->where('t_type','=', 'out')->sum('amount');
        $voucherout = Provoucher::sum('amount');
        $commission = Commission::sum('commission');
        $processvoucher = Provoucher::count();
        return view('admin.dashboard',compact('donation','transaction','voucherout','commission','processvoucher'));
    }
}
