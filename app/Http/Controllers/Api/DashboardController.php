<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Charity;
use Illuminate\Http\Request;
use App\Models\Usertransaction;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {

        $pending_transactions = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
        ])->sum('amount');


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

        $tamount = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->get();
            
        $alltransactions = Usertransaction::where([
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=', auth()->user()->id],
                ['pending','=', '1']
            ])->orderBy('id','DESC')->limit(5)->get();

            // previous balance calculation
        $preamntIn = Usertransaction::where([
                ['t_type','=', 'In'],
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['t_type','=', 'In'],
                ['user_id','=', auth()->user()->id],
                ['pending','=', '1']
            ])->orderBy('id','DESC')->sum('amount');

        $precommission = Usertransaction::where([
                ['t_type','=', 'In'],
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['t_type','=', 'In'],
                ['user_id','=', auth()->user()->id],
                ['pending','=', '1']
            ])->orderBy('id','DESC')->sum('commission');

        $preamntOut = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['t_type','=', 'Out'],
                ['user_id','=', auth()->user()->id],
                ['pending','=', '1']
            ])->orderBy('id','DESC')->sum('amount');

        $prebalance = $preamntIn + $precommission - $preamntOut;
            // previous balance calculation

        $responseArray = [
            'status'=>'ok',
            'pending_transactions'=>$pending_transactions,
            'currentyramount'=>$currentyramount,
            'totalamount'=>$totalamount,
            'tamount'=>$tamount,
            'prebalance'=>$prebalance,
            'preamntIn'=>$preamntIn,
            'precommission'=>$precommission,
            'preamntOut'=>$preamntOut,
            'alltransactions'=>$alltransactions
        ]; 
        return response()->json($responseArray,200);
    }

    
    

    public function charity_dashboard($id)
    {
        
        $data = Charity::where('id', $id)->first(); 
        // dd($data);
        $pending_transactions = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['charity_id','=', $id],
            ['pending','=', '0']
        ])->sum('amount');

        if($data == null){
            $data = 'Data Not Found';
        }
        $responseArray = [
            'status'=>'ok',
            'data'=>$data,
            'pending_transactions'=>$pending_transactions
        ]; 
        return response()->json($responseArray,200);
    }


    public function get_all_donor()
    {
        
        $data = User::where('is_type', 'user')->get(); 
        

        if($data == null){
            $data = 'Data Not Found';
        }
        $responseArray = [
            'status'=>'ok',
            'data'=>$data
        ]; 
        return response()->json($responseArray,200);
    }
}
