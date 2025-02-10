<?php

namespace App\Console\Commands;

use App\Mail\CampaignReport;
use App\Models\Campaign;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
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

        // campaign report part start
        $campaign = Campaign::where('end_date', '<=', $current_date)->where('emailsend', 0)->get();
        foreach ($campaign as $key => $campaign) {
            $data = Usertransaction::select('id', 'user_id','amount','campaign_id','created_at')->whereNotNull('campaign_id')->orderBy('id','DESC')->where('campaign_id', $campaign->id)->get();
            $reportid = rand(1000, 9999);

            $charity = Charity::find($campaign->charity_id);
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['cc'] = $contactmail;

            $pdf = PDF::loadView('invoices.campaign_report', compact('data','charity'));
            $output = $pdf->output();
            file_put_contents(public_path().'/invoices/'.'Report#'.$reportid.'.pdf', $output);
            
            $array['name'] = $charity->name;
            $array['view'] = 'mail.campaignreport';
            $array['subject'] = 'Campaign report';
            $array['from'] = 'info@tevini.co.uk';
            $array['content'] = 'Hi, Your campaign report has been placed';
            $array['file'] = public_path().'/invoices/Report#'.$reportid.'.pdf';
            $array['file_name'] = 'Report#'.$reportid.'.pdf';
            $array['subjectsingle'] = 'Report Placed - '.$reportid;
            Mail::to($charity->email)->cc($contactmail)->send(new CampaignReport($array));

            $updateCampaign = Campaign::find($campaign->id);
            $updateCampaign->emailsend = 1;
            $updateCampaign->save();
        }
        // campaign report part end

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

                    // payment == 2 means Continuous payments 

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

                        // email
                        $contactmail = ContactMail::where('id', 1)->first()->name;
                        $array['cc'] = $contactmail;
                        $array['name'] = $user->name;
                        $array['email'] = $user->email;
                        $array['phone'] = $user->phone;
                        $array['amount'] = $activestand_order->amount;
                        $array['charity'] = $charity->name;
                        $array['charityEmail'] = $charity->email;
                        $email = $user->email;
                        $array['from'] = 'info@tevini.co.uk';
                        $array['subject'] = 'Standing order donation';
                        $array['charity_subject'] = 'Standing order donation';

                        Mail::send('mail.standingDonation', compact('array'), function($message)use($array,$email) {
                            $message->from($array['from'], 'Tevini.co.uk');
                            $message->to($email)->cc($array['cc'])->subject($array['subject']);
                        });

                        Mail::send('mail.standingDonationCharity', compact('array'), function($message)use($array,$charity) {
                            $message->from($array['from'], 'Tevini.co.uk');
                            $message->to($charity->email)->cc($array['cc'])->subject($array['charity_subject']);
                        });
                        // email

                    }

                }elseif($activestand_order->payments == 1){
                    
                    // payment == 1 means Fixed number of payments

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

                        // email
                        $contactmail = ContactMail::where('id', 1)->first()->name;
                        $array['cc'] = $contactmail;
                        $array['name'] = $user->name;
                        $array['email'] = $user->email;
                        $array['phone'] = $user->phone;
                        $array['amount'] = $activestand_order->amount;
                        $array['charity'] = $charity->name;
                        $array['charityEmail'] = $charity->email;
                        $email = $user->email;
                        $array['from'] = 'info@tevini.co.uk';
                        $array['subject'] = 'Standing order donation';
                        $array['charity_subject'] = 'Standing order donation';

                        Mail::send('mail.standingDonation', compact('array'), function($message)use($array,$email) {
                            $message->from($array['from'], 'Tevini.co.uk');
                            $message->to($email)->cc($array['cc'])->subject($array['subject']);
                        });

                        Mail::send('mail.standingDonationCharity', compact('array'), function($message)use($array,$charity) {
                            $message->from($array['from'], 'Tevini.co.uk');
                            $message->to($charity->email)->cc($array['cc'])->subject($array['charity_subject']);
                        });
                        // email

                    }


                }

        }
        return 0;
    }


}
