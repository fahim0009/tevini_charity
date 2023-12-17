<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\StandingDonation;
use App\Models\StandingdonationDetail;
use App\Models\Charity;
use App\Models\User;
use App\Models\Donation;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DeductStandingDonation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donation:deduct-standing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deduct standing donation amount from donor balance & add to charity balance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $current_date = now()->format('Y-m-d'); // current date with server format

        $activestand_orders = StandingDonation::where('status', '=' ,'1')->orderBy('id','DESC')->get();

        
        foreach($activestand_orders as $activestand_order)
        {
            
            $donationdetails = StandingdonationDetail::where('standing_donation_id', '=' , $activestand_order->id)->orderBy('id', 'desc')->first();

                if(isset($donationdetails)){
                    $start_date = $donationdetails->instalment_date;
                }else{
                    $start_date = $activestand_order->starting;
                }

                $start_date_carbon = Carbon::parse($start_date);
                $instalment_date_carbon = $start_date_carbon->addMonths($activestand_order->interval);
                $instalment_date = $instalment_date_carbon->format('Y-m-d');
                
                if($activestand_order->payments == 2){


                    if (($current_date >= $instalment_date)) {

                                $doncaldetl = new StandingdonationDetail;
                                $doncaldetl->standing_donation_id = $activestand_order->id;
                                $doncaldetl->user_id = $activestand_order->user_id;
                                $doncaldetl->charity_id = $activestand_order->charity_id;
                                $doncaldetl->amount = $activestand_order->amount;
                                $doncaldetl->instalment_date = $instalment_date;
                                $doncaldetl->instalment_mode = "continuous";
                                $doncaldetl->status = 0;
                                $doncaldetl->save();

                                $utransaction = new Usertransaction();
                                $utransaction->t_id = time() . "-" . $activestand_order->user_id;
                                $utransaction->user_id = $activestand_order->user_id;
                                $utransaction->charity_id = $activestand_order->charity_id;
                                $utransaction->standing_donationdetails_id = $activestand_order->id;
                                $utransaction->t_type = "Out";
                                $utransaction->amount =   $activestand_order->amount;
                                $utransaction->title =  "Standing order donation";
                                $utransaction->status =  1;
                                $utransaction->save();

                                $user = User::find($activestand_order->user_id);
                                $user->decrement('balance',$activestand_order->amount);
                                $user->save();

                                // card balance update
                                if (isset($user->CreditProfileId)) {
                                    $CreditProfileId = $user->CreditProfileId;
                                    $CreditProfileName = $user->name;
                                    $AvailableBalance = 0 - $activestand_order->amount;
                                    $comment = "Make a donation or Standing order";
                                    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                                        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                                            'CreditProfileId' => $CreditProfileId,
                                            'CreditProfileName' => $CreditProfileName,
                                            'AvailableBalance' => $AvailableBalance,
                                            'comment' => $comment,
                                        ]);
                                }
                                // card balance update end
        
                                $charity = Charity::find($activestand_order->charity_id);
                                $charity->increment('balance',$activestand_order->amount);
                                $charity->save();

                    }

                }elseif($activestand_order->payments == 1){

                    if (($current_date >= $instalment_date) && ($activestand_order->payment_made < $activestand_order->number_payments)) {

                        $doncaldetl = new StandingdonationDetail;
                        $doncaldetl->standing_donation_id = $activestand_order->id;
                        $doncaldetl->user_id = $activestand_order->user_id;
                        $doncaldetl->charity_id = $activestand_order->charity_id;
                        $doncaldetl->amount = $activestand_order->amount;
                        $doncaldetl->instalment_date = $instalment_date;
                        $doncaldetl->instalment_mode = "Fiexed";
                        $doncaldetl->status = 0;
                        $doncaldetl->save();

                        $utransaction = new Usertransaction();
                        $utransaction->t_id = time() . "-" . $activestand_order->user_id;
                        $utransaction->user_id = $activestand_order->user_id;
                        $utransaction->charity_id = $activestand_order->charity_id;
                        $utransaction->standing_donationdetails_id = $activestand_order->id;
                        $utransaction->t_type = "Out";
                        $utransaction->amount =   $activestand_order->amount;
                        $utransaction->title =  "Standing order donation";
                        $utransaction->status =  1;
                        $utransaction->save();

                        $user = User::find($activestand_order->user_id);
                        $user->decrement('balance',$activestand_order->amount);
                        $user->save();

                        // card balance update
                        if (isset($user->CreditProfileId)) {
                            $CreditProfileId = $user->CreditProfileId;
                            $CreditProfileName = $user->name;
                            $AvailableBalance = 0 - $activestand_order->amount;
                            $comment = "Make a donation or Standing order";
                            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                                    'CreditProfileId' => $CreditProfileId,
                                    'CreditProfileName' => $CreditProfileName,
                                    'AvailableBalance' => $AvailableBalance,
                                    'comment' => $comment,
                                ]);
                        }
                        // card balance update end

                        $charity = Charity::find($activestand_order->charity_id);
                        $charity->increment('balance',$activestand_order->amount);
                        $charity->save();

                        $standing_order = StandingDonation::find($activestand_order->id);
                        $standing_order->increment('payment_made',1);
                        $standing_order->save();

                    }


                }

        }
        return 0;
    }


}
