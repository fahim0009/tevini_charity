<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccDelRequest;
use App\Models\Charity;
use App\Models\CharityLink;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use App\Models\Usertransaction;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Date;
use Illuminate\support\Facades\Auth;

class DashboardController extends Controller
{


    public function index()
    {
        $user = auth()->user();

        // Pending transactions sum
        $pending_transactions = Usertransaction::where('user_id', $user->id)
            ->where('t_type', 'Out')
            ->where('pending', '0')
            ->sum('amount');

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

        $finalTotalPrevYear = (int)($user->prev_yr_gift_aid > 0 ? $user->prev_yr_gift_aid : $lastTaxYearAmountExpGiftAid);
        $finalTotalCurrYear = (int)($user->current_yr_gift_aid > 0 ? $user->current_yr_gift_aid : $currentyramountExpGiftAid);

        // ✅ FIX: Safe grouped where conditions
        $tamount = Usertransaction::where('user_id', $user->id)
            ->where(function($q) {
                $q->where('status', '1')->orWhere('pending', '1');
            })
            ->orderBy('id','DESC')
            ->get();
            
        // 5 latest transactions to show on dashboard
        $alltransactions = Usertransaction::where('user_id', $user->id)
            ->where(function($q) {
                $q->where('status', '1')->orWhere('pending', '1');
            })
            ->orderBy('id','DESC')
            ->limit(5)
            ->get();

        // ✅ FIX: Calculate Previous Balance correctly
        $latestbalance = $user->getLiveBalance();

        $tbalance = $latestbalance;
        foreach ($alltransactions as $tran) {
            if ($tran->t_type == "In") {
                $tbalance = $tbalance - $tran->amount;
                if ($tran->commission > 0) {
                    $tbalance = $tbalance - $tran->commission;
                }
            } elseif ($tran->t_type == "Out") {
                $tbalance = $tbalance + $tran->amount;
            }
        }

        // ✅ FIX: Calculate pre-amounts safely
        $preamntIn = Usertransaction::where('user_id', $user->id)
            ->where('t_type', 'In')
            ->where(function($q) {
                $q->where('status', '1')->orWhere('pending', '1');
            })
            ->sum('amount');

        $precommission = Usertransaction::where('user_id', $user->id)
            ->where('t_type', 'In')
            ->where(function($q) {
                $q->where('status', '1')->orWhere('pending', '1');
            })
            ->sum('commission');

        $preamntOut = Usertransaction::where('user_id', $user->id)
            ->where('t_type', 'Out')
            ->where(function($q) {
                $q->where('status', '1')->orWhere('pending', '1');
            })
            ->sum('amount');

        $donation_req = CharityLink::where('email', $user->email)
            ->where('donor_notification','0')
            ->get();

        $responseArray = [
            'status'=>'ok',
            'pending_transactions'=>$pending_transactions,
            'currentyramount'=>$finalTotalCurrYear,
            'totalamount'=>$finalTotalPrevYear,
            'tamount'=>$tamount,
            'previousBalance'=>$tbalance, 
            'preamntIn'=>$preamntIn,
            'precommission'=>$precommission,
            'preamntOut'=>$preamntOut,
            'alltransactions'=>$alltransactions,
            'donation_request'=>$donation_req,
        ]; 
        
        return response()->json($responseArray, 200);
    }
    
    

    public function charity_dashboard($id)
    {
        
        $data = Charity::where('id', $id)->first(); 
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
        
        $data = User::where('is_type', 'user')->select('id','name', 'email')->get(); 
        

        if($data == null){
            $data = 'Data Not Found';
        }
        $responseArray = [
            'status'=>'ok',
            'data'=>$data
        ]; 
        return response()->json($responseArray,200);
    }


    public function accountDeleteRequest(Request $request)
    {
        $callback = new AccDelRequest();
        $callback->user_id = Auth::id();
        $callback->date = Date::now()->format('Y-m-d');
        if ($callback->save()) {

            return response()->json([
                'success' => true,
                'message' => 'Account delete request sent successfully.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request a callback.'
            ], 202);
        }
    }

    // app version check
    public function userBalance()
    {
        $user = auth()->user();

        // Get balance from the User model's getLiveBalance() method
        $balance = $user->getLiveBalance();

        $data = [
            'balance' => number_format($balance, 2)
        ];

        $responseArray = [
            'status' => 'ok',
            'data' => $data
        ]; 

        return response()->json($responseArray, 200);
    }

    // address finder api
    public function addressFinderApi()
    {
        
        $data = CompanyDetail::select('id','address_finder_api')->first(); 
        
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
