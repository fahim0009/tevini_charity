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

        // previous year data start
        $period = CarbonPeriod::create(
            now()->month(4)->subMonths(12)->startOfMonth()->format('Y-m-d'),
            '1 month',
            now()->month(3)->endOfMonth()->format('Y-m-d')
        );
        $finYear = [];
        $totalamount = 0;
        foreach ($period as $p) {
            $finYear[] = $p->format('m-Y');
            $currentmonthgift2 = Usertransaction::where('user_id','=', auth()->user()->id)->where('gift','=','1')
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', $p->format('m'))
                            ->whereYear('created_at', $p->format('Y'))
                            ->get();
            foreach ($currentmonthgift2 as $data){
                $totalamount = $data->amount + $totalamount + $data->commission;
            }
        }
        // previous year data end

        // current year data start
        $currentyr = Usertransaction::where('user_id','=', auth()->user()->id)->where('gift','=','1')
                        ->whereBetween('created_at',
                            [Carbon::now()->subMonth(4), Carbon::now()]
                        )
                        ->get();

        $currentyramount = 0;
        foreach ($currentyr as $data2){
            $currentyramount = $data2->amount + $currentyramount + $data2->commission;
        }
        // current year data end

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
        return view('frontend.user.dashboard',compact('currentyramount','totalamount','donorUpBalance'));

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
