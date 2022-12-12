<?php

namespace App\Http\Controllers;

use App\Models\Usertransaction;
use App\Models\DonationCalculator;
use App\Models\DonationDetail;
use Carbon\Carbon;
use DateTime;
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
                $totalamount = $data->amount + $totalamount + $data->commission;
            }
        }
        // previous year data end

        // current year data start
        $currentyr = Usertransaction::where('user_id','=', Auth::user()->id)->where('gift','=','1')
                        ->whereBetween('created_at',
                            [Carbon::now()->subMonth(4), Carbon::now()]
                        )
                        ->get();

        $currentyramount = 0;
        foreach ($currentyr as $data2){
            $currentyramount = $data2->amount + $currentyramount + $data2->commission;
        }
        // current year data end



        #############################################################################################

        // donation calculaton start
        $dt = Carbon::now();
        $sub = $dt->month - 4; // sub month will change in every month
        $start_date = Carbon::now()->subMonth($sub); //start date - cut off date.
        $diff = $start_date->diffInDays($dt); // different days between current date & start date.


        $totaltran = Usertransaction::where('user_id', Auth::user()->id)
                                    ->where('t_type','=','Out')
                                    ->whereBetween('created_at',
                                        [Carbon::now()->subMonth($sub), Carbon::now()]
                                    )
                                    ->sum('amount');


        $donationamnt = DonationDetail::where('donor_id', Auth::user()->id)
                    ->whereBetween('date',
                        [Carbon::now()->subMonth(4), Carbon::now()]
                    )
                    ->sum('donation_amount');

        if($donationamnt){
            $availabledonation = $totaltran - $donationamnt;

        }else{
            $availabledonation = 0;
        }
        $donor_cal = DonationCalculator::where('donor_id', Auth::user()->id)->first();




        if(empty($donor_cal)){

            $msg = "Fill this form for donation calculation";
            $dondetails = DonationDetail::where('donor_id','=', Auth::user()->id)->get();
            return view('frontend.user.dashboard',compact('currentyramount','totalamount','totaltran','availabledonation','dondetails','msg'));


        }else{

                $donationdetails = DonationDetail::where('donor_id', Auth::user()->id)->orderBy('id', 'DESC')->first();
                $last_date = Carbon::parse($donationdetails->date);
                $diff_with_lastdate = $last_date->diffInDays($dt);

            if ($donationdetails) {

                        if($diff_with_lastdate >= $donor_cal->income_slot){
                        for($x=0; $x < $diff_with_lastdate; $x+=$donor_cal->income_slot){
                            $doncaldetl = new DonationDetail;
                            $doncaldetl->donor_id = Auth::user()->id;
                            $doncaldetl->donation_cal_id = $donor_cal->id;
                            $doncaldetl->date = $last_date->addDays($x);
                            $doncaldetl->income_amount = $donor_cal->income_amount;
                            $doncaldetl->income_slot = $donor_cal->income_slot;
                            $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                            $doncaldetl->available_for_donation = $totaltran;
                            $doncaldetl->save();
                        }
                    }


            } else {

                for($x=0; $x < $diff; $x+=$donor_cal->income_slot){
                    $doncaldetl = new DonationDetail;
                    $doncaldetl->donor_id = Auth::user()->id;
                    $doncaldetl->donation_cal_id = $donor_cal->id;
                    $doncaldetl->date = Carbon::now()->subMonth(8)->addDays($x);
                    $doncaldetl->income_amount = $donor_cal->income_amount;
                    $doncaldetl->income_slot = $donor_cal->income_slot;
                    $doncaldetl->donation_amount = $donor_cal->income_amount * ($donor_cal->donation_percentage/100);
                    $doncaldetl->available_for_donation = $totaltran;
                    $doncaldetl->save();
                }

            }
    }

        $dondetails = DonationDetail::where('donor_id','=', Auth::user()->id)->get();
        return view('frontend.user.dashboard',compact('currentyramount','totalamount','totaltran','availabledonation','dondetails'));

    }

    public function sellerHome()
    {
        return view('seller.dashboard');
    }
}
