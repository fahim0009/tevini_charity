<?php

namespace App\Http\Controllers;

use App\Models\Usertransaction;
use App\Models\DonationCalculator;
use App\Models\DonationDetail;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Carbon\CarbonPeriod;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home');
        if (auth()->user()->is_type == 'admin') {
            return redirect()->route('admin.dashboard');
        }if (auth()->user()->is_type == 'staff') {
            return redirect()->route('admin.dashboard');
        }if (auth()->user()->is_type == 'agent') {
            return redirect()->route('agent.dashboard');
        }if (auth()->user()->is_type == 'seller') {
            return redirect()->route('seller.dashboard');
        }if (auth()->user()->is_type == 'user') {

            if (auth()->user()->passwordchk == '0') {
                return redirect()->route('user.chkpassword');
            } else {
                return redirect()->route('user.dashboard');
            }
            

        }
        if (auth()->user()->is_type == 0) {
            return view('home');
        }
    }
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function agentHome()
    {
        return view('agent.dashboard');
    }

public function userHome()
{
    $user = auth()->user();

    // 1. Core Balances (Using your Model methods)
    $donorUpBalance = $user->getLiveBalance();
    
    $pending_transactions = Usertransaction::where('user_id', $user->id)
        ->where('t_type', 'Out')
        ->where('pending', '0') 
        ->sum('amount');

    // 2. Gift Aid Stats (Financial Year: April to March)
    $currentTaxYearStart = now()->month >= 4 ? now()->month(4)->startOfMonth() : now()->subYear()->month(4)->startOfMonth();
    $lastTaxYearStart = $currentTaxYearStart->copy()->subYear();
    
    $currentyramount = Usertransaction::where('user_id', $user->id)
        ->where('gift', 1)->where('status', 1)
        ->whereBetween('created_at', [$currentTaxYearStart, now()])
        ->selectRaw('SUM(amount + commission) as total')->value('total') ?? 0;

        $currentyramountExpGiftAid = $currentyramount * 0.25;

    $lastTaxYearAmount = Usertransaction::where('user_id', $user->id)
        ->where('gift', 1)->where('status', 1)
        ->whereBetween('created_at', [$lastTaxYearStart, $currentTaxYearStart->copy()->subDay()])
        ->selectRaw('SUM(amount + commission) as total')->value('total') ?? 0;

        $lastTaxYearAmountExpGiftAid = $lastTaxYearAmount * 0.25;

    // 3. Transactions & Notifications
    // Optimization: Use eager loading 'provoucher' and 'charity' to prevent N+1 issues
    $alltransactions = Usertransaction::where('user_id', $user->id)
        ->where(fn($q) => $q->where('status', 1)->orWhere('pending', 1))
        ->with(['provoucher', 'charity'])
        ->orderBy('id', 'DESC')
        ->limit(5)
        ->get();

    $donation_req = \App\Models\CharityLink::where('email', $user->email)
        ->where('donor_notification', '0')
        ->get();

    return view('frontend.user.dashboard', compact(
        'donorUpBalance', 
        'pending_transactions', 
        'currentyramountExpGiftAid', 
        'lastTaxYearAmountExpGiftAid', 
        'alltransactions', 
        'donation_req'
    ));
}




    public function changePassword()
    {
        return view('frontend.user.passwordchange');
    }

    public function passwordChange(Request $request)
    {


      if ($request['password']) {
            $request->validate([
              'password' => [
                                'required',
                                Password::min(8)
                                    ->letters()
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols()
              ],
              
              'password_confirmation' => 'required|same:password'
            ]);

           
      }

          $data = User::find(Auth::user()->id);
          $data->password = Hash::make($request['password']);
          $data->passwordchk = '1';
          $data->save();
           return redirect()->back()->with('success', 'User Updated Successfully'); 
  	}

    public function sellerHome()
    {
        return view('seller.dashboard');
    }
    
}
