<?php

namespace App\Http\Controllers;

use App\Models\Usertransaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\support\Facades\Auth;
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
            return redirect()->route('user.dashboard');
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
            $currentmonthgift2 = Usertransaction::where('user_id','=', Auth::user()->id)->where('gift','=','1')
                            ->where('gift','=', 1)
                            ->whereMonth('created_at', $p->format('m'))
                            ->whereYear('created_at', $p->format('Y'))
                            ->get();
            foreach ($currentmonthgift2 as $data){
                // dd($data);
                $data->amount;
                $totalamount = $data->amount + $totalamount;
            }
        }
        // previous year data end

        // current year data start
        $currentyr = Usertransaction::where('user_id','=', Auth::user()->id)->where('gift','=','1')
                        ->whereBetween('created_at',
                            [Carbon::now()->subMonth(4), Carbon::now()]
                        )
                        ->get();

                        // dd($items );

        $currentyramount = 0;
        foreach ($currentyr as $data2){
            $data2->amount;
            $currentyramount = $data2->amount + $currentyramount;
        }
        // current year data end


        return view('frontend.user.dashboard',compact('currentyramount','totalamount'));
    }
    public function sellerHome()
    {
        return view('seller.dashboard');
    }
}
