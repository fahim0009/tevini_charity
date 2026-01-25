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

        $pending_transactions = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['user_id','=', auth()->user()->id],
            ['pending','=', '0']
        ])->sum('amount');

        $user = auth()->user();


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

        $finalTotalPrevYear = $user->prev_yr_gift_aid > 0 ? $user->prev_yr_gift_aid : $totalamount;
        $finalTotalCurrYear = $user->current_yr_gift_aid > 0 ? $user->current_yr_gift_aid : $currentyramount;
        $finalExpectedGiftAid = $user->gift_aid_currenction > 0 ? $user->gift_aid_currenction : $user->expected_gift_aid;

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



        $tinAmt = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1'],
            ['t_type','=', 'In']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1'],
            ['t_type','=', 'In']
            ])->orderBy('id','DESC')->sum('amount');

        $toutAmt = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1'],
            ['t_type','=', 'Out']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1'],
            ['t_type','=', 'Out']
            ])->orderBy('id','DESC')->sum('amount');


        $latestbalance = $tinAmt - $toutAmt;

        $gettrans = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
            ])->orderBy('id','DESC')->limit(5)->get();

            $tbalance = $latestbalance;

            foreach ($gettrans as $key => $tran) {
                if ($tran->t_type == "In") {
                    $tbalance = $tbalance - $tran->amount;
                }elseif ($tran->t_type == "Out") {
                    $tbalance = $tbalance + $tran->amount;
                } else {
                    # code...
                }
            }
            
        $donation_req = CharityLink::where('email',auth()->user()->email)->where('donor_notification','0')->get();


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
        return response()->json($responseArray,200);
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

        // donor balance
        $userTransactionBalance = UserTransaction::selectRaw('
                SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
            ')
            ->where([
                ['user_id','=', auth()->user()->id],
                ['status','=', '1']
            ])->orwhere([
                ['user_id','=', auth()->user()->id],
                ['pending','=', '1']
            ])
            ->first();
        // donor balance end

        $data = [
                    'balance' => number_format($userTransactionBalance->balance, 2)
                ];
        
        if($data == null){
            $data = 'Data Not Found';
        }
        $responseArray = [
            'status'=>'ok',
            'data'=>$data
        ]; 
        return response()->json($responseArray,200);
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
