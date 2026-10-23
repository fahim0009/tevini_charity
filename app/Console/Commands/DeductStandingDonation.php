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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        
        $current_date = now()->format('Y-m-d');
        $current_date_carbon = Carbon::parse($current_date);

        // Counters for summary log
        $campaignsProcessed = 0;
        $campaignsFailed = 0;
        $donationsProcessed = 0;
        $donationsSkipped = 0;
        $donationsFailed = 0;

        Log::info('========== DeductStandingDonation command started ==========', [
            'current_date' => $current_date,
        ]);

        // ============================================================
        // Campaign report part start
        // ============================================================
        $campaigns = Campaign::where('end_date', '<=', $current_date)
            ->where('emailsend', 0)
            ->get();

        Log::info('Campaigns pending report', [
            'count' => $campaigns->count(),
        ]);

        foreach ($campaigns as $campaign) {
            try {
                $charity = Charity::find($campaign->charity_id);
                if (!$charity) {
                    Log::error('Charity not found for campaign, skipping', [
                        'campaign_id' => $campaign->id,
                        'charity_id'  => $campaign->charity_id,
                    ]);
                    $campaignsFailed++;
                    continue;
                }

                $contactMailModel = ContactMail::where('id', 1)->first();
                if (!$contactMailModel) {
                    Log::error('ContactMail record not found (id=1), skipping campaign report');
                    $campaignsFailed++;
                    continue;
                }
                $contactmail = $contactMailModel->name;

                $data = Usertransaction::select('id', 'user_id', 'amount', 'campaign_id', 'created_at')
                    ->whereNotNull('campaign_id')
                    ->where('campaign_id', $campaign->id)
                    ->orderBy('id', 'DESC')
                    ->get();

                $reportid = rand(1000, 9999);

                $array = [];
                $array['cc'] = $contactmail;

                $pdf = PDF::loadView('invoices.campaign_report', compact('data', 'charity'));
                $output = $pdf->output();
                $pdfPath = public_path() . '/invoices/Report#' . $reportid . '.pdf';
                file_put_contents($pdfPath, $output);

                $array['name'] = $charity->name;
                $array['view'] = 'mail.campaignreport';
                $array['subject'] = 'Campaign report';
                $array['from'] = 'info@tevini.co.uk';
                $array['content'] = 'Hi, Your campaign report has been placed';
                $array['file'] = $pdfPath;
                $array['file_name'] = 'Report#' . $reportid . '.pdf';
                $array['subjectsingle'] = 'Report Placed - ' . $reportid;

                Mail::to($charity->email)->cc($contactmail)->send(new CampaignReport($array));

                $updateCampaign = Campaign::find($campaign->id);
                $updateCampaign->emailsend = 1;
                $updateCampaign->save();

                $campaignsProcessed++;
                Log::info('Campaign report sent successfully', [
                    'campaign_id'  => $campaign->id,
                    'charity_name' => $charity->name,
                    'report_id'    => $reportid,
                    'transactions' => $data->count(),
                ]);
            } catch (\Exception $e) {
                $campaignsFailed++;
                Log::error('Failed to process campaign report', [
                    'campaign_id' => $campaign->id,
                    'error'       => $e->getMessage(),
                    'trace'       => $e->getTraceAsString(),
                ]);
                continue;
            }
        }
        // ============================================================
        // Campaign report part end
        // ============================================================

        // Map string-based interval labels to numeric month values
        // Map string-based interval labels to numeric month values
        $intervalMap = [
            'Monthly'   => 1,
            'Quarterly' => 3,
            'Yearly'    => 12,
            'Annually'  => 12,
            'Weekly'    => 0,
            'Bi-Weekly' => 0,
        ];

        $activestand_orders = StandingDonation::where('status', '=', '1')
            ->orderBy('id', 'DESC')
            ->get();

        Log::info('Active standing donations found', [
            'count' => $activestand_orders->count(),
        ]);

        foreach ($activestand_orders as $activestand_order) {
            try {
                $array = [];

                // ----------------------------------------------------------
                // Validate essential fields
                // ----------------------------------------------------------
                if (!$activestand_order->user_id || !$activestand_order->charity_id || !$activestand_order->amount) {
                    Log::warning('Standing donation has missing required fields, skipping', [
                        'standing_donation_id' => $activestand_order->id,
                    ]);
                    $donationsSkipped++;
                    continue;
                }

                if (!$activestand_order->starting) {
                    Log::warning('Standing donation has no starting date, skipping', [
                        'standing_donation_id' => $activestand_order->id,
                    ]);
                    $donationsSkipped++;
                    continue;
                }

                // ----------------------------------------------------------
                // Check if Fixed donation is already completed
                // ----------------------------------------------------------
                if ($activestand_order->payments == 1 && $activestand_order->payment_made >= $activestand_order->number_payments) {
                    $activestand_order->status = 0; // Mark as inactive
                    $activestand_order->save();
                    Log::info('Fixed standing donation completed, deactivated', [
                        'standing_donation_id' => $activestand_order->id,
                    ]);
                    continue;
                }

                // ----------------------------------------------------------
                // Determine expected instalment date (from last detail or starting field)
                // ----------------------------------------------------------
                $donationdetails = StandingdonationDetail::where('standing_donation_id', '=', $activestand_order->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if (isset($donationdetails)) {
                    $last_date_carbon = Carbon::parse($donationdetails->instalment_date);
                } else {
                    $last_date_carbon = Carbon::parse($activestand_order->starting);
                }

                // ----------------------------------------------------------
                // Resolve interval to numeric months
                // ----------------------------------------------------------
                $intervalValue = $activestand_order->interval;
                if (is_string($intervalValue) && isset($intervalMap[$intervalValue])) {
                    $intervalMonths = $intervalMap[$intervalValue];
                } elseif (is_numeric($intervalValue)) {
                    $intervalMonths = (int) $intervalValue;
                } else {
                    $intervalMonths = 1;
                    Log::warning('Unknown interval value, defaulting to 1 (monthly)', [
                        'standing_donation_id' => $activestand_order->id,
                        'interval_value'        => $intervalValue,
                    ]);
                }

                // Prevent infinite loops for weekly/bi-weekly (fallback to 1 month)
                if ($intervalMonths === 0) {
                    $intervalMonths = 1;
                }

                $expected_date = $last_date_carbon->copy()->addMonths($intervalMonths);

                Log::info('Standing donation date calculation', [
                    'standing_donation_id' => $activestand_order->id,
                    'user_id'              => $activestand_order->user_id,
                    'last_date'            => $last_date_carbon->format('Y-m-d'),
                    'interval_months'      => $intervalMonths,
                    'expected_date'        => $expected_date->format('Y-m-d'),
                    'current_date'         => $current_date,
                    'payments_type'        => $activestand_order->payments,
                    'payment_made'         => $activestand_order->payment_made,
                    'number_payments'      => $activestand_order->number_payments,
                ]);

                // ============================================================
                // NEW LOGIC: Check if the expected month has completely passed
                // ============================================================
                $is_past_expected_month = $current_date_carbon->format('Y-m') > $expected_date->format('Y-m');

                if ($is_past_expected_month) {
                    // The month has passed without a successful payment. Permanently skip it.
                    DB::transaction(function () use ($activestand_order, $expected_date) {

                        // FIX: Check if instalment already exists (defensive)
                        $alreadyExists = StandingdonationDetail::where('standing_donation_id', $activestand_order->id)
                            ->where('instalment_date', $expected_date->format('Y-m-d'))
                            ->exists();
                        
                        if ($alreadyExists) {
                            Log::info('Instalment already exists, skipping', [
                                'standing_donation_id' => $activestand_order->id,
                                'instalment_date'       => $expected_date->format('Y-m-d'),
                            ]);
                            return; 
                        }

                        $doncaldetl = new StandingdonationDetail;
                        $doncaldetl->standing_donation_id = $activestand_order->id;
                        $doncaldetl->user_id = $activestand_order->user_id;
                        $doncaldetl->charity_id = $activestand_order->charity_id;
                        $doncaldetl->amount = 0; // 0 amount because it was skipped
                        $doncaldetl->instalment_date = $expected_date->format('Y-m-d');
                        $doncaldetl->instalment_mode = $activestand_order->payments == 1 ? "Fixed" : "continuous";
                        $doncaldetl->status = 2; // 2 = Skipped due to insufficient balance
                        $doncaldetl->save();

                        // Increment payment_made so total interval count decreases for fixed plans
                        if ($activestand_order->payments == 1) {
                            $activestand_order->increment('payment_made', 1);
                            
                            // Check if plan is completed after skipping
                            if ($activestand_order->payment_made >= $activestand_order->number_payments) {
                                $activestand_order->status = 0; // Deactivate
                            }
                            $activestand_order->save();
                        }
                    });

                    Log::warning('Standing donation permanently skipped due to insufficient balance for the month', [
                        'standing_donation_id' => $activestand_order->id,
                        'expected_date'         => $expected_date->format('Y-m-d'),
                    ]);
                    $donationsSkipped++;
                    continue; // Move to next donation
                }

                // ============================================================
                // Process Payment if current date >= expected date
                // ============================================================
                if ($current_date_carbon->gte($expected_date)) {
                    
                    $user = User::find($activestand_order->user_id);
                    if (!$user) {
                        Log::error('User not found for standing donation', [
                            'standing_donation_id' => $activestand_order->id,
                            'user_id'              => $activestand_order->user_id,
                        ]);
                        $donationsFailed++;
                        continue;
                    }

                    $availableLimit = $user->getAvailableLimit();
                    if ($availableLimit < $activestand_order->amount) {
                        Log::info('Standing donation waiting for balance in current month', [
                            'standing_donation_id' => $activestand_order->id,
                            'user_id'              => $user->id,
                            'available_limit'       => $availableLimit,
                            'amount'                => $activestand_order->amount,
                        ]);
                        // Do not skip permanently yet, wait until the month ends
                        $donationsSkipped++;
                        continue;
                    }

                    // Enough balance! Process payment inside DB transaction
                    DB::transaction(function () use ($activestand_order, $expected_date) {

                        // FIX: Check if skip record already exists
                        $alreadyExists = StandingdonationDetail::where('standing_donation_id', $activestand_order->id)
                            ->where('instalment_date', $expected_date->format('Y-m-d'))
                            ->exists();
                        
                        if ($alreadyExists) {
                            return; 
                        }



                        $doncaldetl = new StandingdonationDetail;
                        $doncaldetl->standing_donation_id = $activestand_order->id;
                        $doncaldetl->user_id = $activestand_order->user_id;
                        $doncaldetl->charity_id = $activestand_order->charity_id;
                        $doncaldetl->amount = $activestand_order->amount;
                        $doncaldetl->instalment_date = $expected_date->format('Y-m-d'); // Lock to expected date
                        $doncaldetl->instalment_mode = $activestand_order->payments == 1 ? "Fixed" : "continuous";
                        $doncaldetl->status = 0;
                        $doncaldetl->save();

                        $utransaction = new Usertransaction();
                        $utransaction->t_id = time() . "-" . $activestand_order->user_id;
                        $utransaction->user_id = $activestand_order->user_id;
                        $utransaction->charity_id = $activestand_order->charity_id;
                        $utransaction->standing_donationdetails_id = $activestand_order->id;
                        $utransaction->t_type = "Out";
                        $utransaction->amount = $activestand_order->amount;
                        $utransaction->title = "Standing order donation";
                        $utransaction->status = 1;
                        $utransaction->save();

                        $u = User::find($activestand_order->user_id);
                        $u->decrement('balance', $activestand_order->amount);

                        $ch = Charity::find($activestand_order->charity_id);
                        $ch->increment('balance', $activestand_order->amount);

                        // Increment payment_made if Fixed
                        if ($activestand_order->payments == 1) {
                            $activestand_order->increment('payment_made', 1);
                            
                            if ($activestand_order->payment_made >= $activestand_order->number_payments) {
                                $activestand_order->status = 0; // Deactivate
                            }
                            $activestand_order->save();
                        }
                    });

                    // Send Email Notification
                    $user = User::find($activestand_order->user_id);
                    $charity = Charity::find($activestand_order->charity_id);
                    $contactMailModel = ContactMail::where('id', 1)->first();
                    $contactmail = $contactMailModel ? $contactMailModel->name : null;

                    if ($user && $charity) {
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

                        try {
                            Mail::send('mail.standingDonation', compact('array'), function ($message) use ($array, $email) {
                                $message->from($array['from'], 'Tevini.co.uk');
                                $message->to($email)->cc($array['cc'])->subject($array['subject']);
                            });
                        } catch (\Exception $e) {
                            Log::error('Failed to send standing donation email', [
                                'standing_donation_id' => $activestand_order->id,
                                'error'                => $e->getMessage(),
                            ]);
                        }
                    }

                    $donationsProcessed++;
                    Log::info('Standing donation processed successfully', [
                        'standing_donation_id' => $activestand_order->id,
                        'amount'               => $activestand_order->amount,
                        'instalment_date'      => $expected_date->format('Y-m-d'),
                    ]);

                } else {
                    Log::info('Standing donation not yet due', [
                        'standing_donation_id' => $activestand_order->id,
                        'expected_date'         => $expected_date->format('Y-m-d'),
                    ]);
                }

            } catch (\Exception $e) {
                $donationsFailed++;
                Log::error('Failed to process standing donation', [
                    'standing_donation_id' => $activestand_order->id,
                    'error'                => $e->getMessage(),
                ]);
                continue;
            }
        }

        // ============================================================
        // Summary log
        // ============================================================
        Log::info('========== DeductStandingDonation command finished ==========', [
            'campaigns_processed'  => $campaignsProcessed,
            'campaigns_failed'      => $campaignsFailed,
            'donations_processed'   => $donationsProcessed,
            'donations_skipped'     => $donationsSkipped,
            'donations_failed'      => $donationsFailed,
        ]);

        $this->info("Summary:");
        $this->info("  Campaigns  — processed: {$campaignsProcessed}, failed: {$campaignsFailed}");
        $this->info("  Donations  — processed: {$donationsProcessed}, skipped: {$donationsSkipped}, failed: {$donationsFailed}");

        return 0;
    }
}