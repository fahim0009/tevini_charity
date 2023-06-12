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

        $dt = Carbon::now();
        $current_date = $dt->date; // sub month will change in every month

        $activestand_orders = StandingDonation::where('status', '1')->orderBy('id', 'desc')->first();

        foreach($activestand_orders as $activestand_order)
        {
            

            $donationdetails = StandingdonationDetail::where('standing_donation_id', $activestand_orders->id)->orderBy('id', 'desc')->first();

                if(isset($donationdetails)){
                    $start_date = $donationdetails->instalment_date;
                }else{
                    $start_date = $activestand_order->starting;
                }

                $instalment_date = $start_date->addMonth($activestand_order->iterval);


                if($activestand_order->payments == 2){


                    if (($current_date >= $instalment_date)) {

                                $doncaldetl = new StandingdonationDetail;
                                $doncaldetl->standing_donation_id = $activestand_order->id;
                                $doncaldetl->donor_id = $activestand_order->user_id;
                                $doncaldetl->charity_id = $activestand_order->charity_id;
                                $doncaldetl->amount = $activestand_order->amount;
                                $doncaldetl->instalment_date = $instalment_date;
                                $doncaldetl->instalment_mode = "continuous";
                                $doncaldetl->status = 0;
                                $doncaldetl->save();

                    }

                }elseif($activestand_order->payments == 1){

                    if (($current_date >= $instalment_date) && ($current_date >= $instalment_date)) {

                        $doncaldetl = new StandingdonationDetail;
                        $doncaldetl->standing_donation_id = $activestand_order->id;
                        $doncaldetl->donor_id = $activestand_order->user_id;
                        $doncaldetl->charity_id = $activestand_order->charity_id;
                        $doncaldetl->amount = $activestand_order->amount;
                        $doncaldetl->instalment_date = $instalment_date;
                        $doncaldetl->instalment_mode = "Fiexed";
                        $doncaldetl->status = 0;
                        $doncaldetl->save();

            }


                }

        }
        return 0;
    }


}
