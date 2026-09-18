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
                // Reset $array to prevent stale data from previous iteration
                $array = [];

                // ----------------------------------------------------------
                // Validate essential fields
                // ----------------------------------------------------------
                if (!$activestand_order->user_id || !$activestand_order->charity_id || !$activestand_order->amount) {
                    Log::warning('Standing donation has missing required fields, skipping', [
                        'standing_donation_id' => $activestand_order->id,
                        'user_id'               => $activestand_order->user_id,
                        'charity_id'            => $activestand_order->charity_id,
                        'amount'                => $activestand_order->amount,
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
                // Determine start_date (from last detail or from starting field)
                // ----------------------------------------------------------
                $donationdetails = StandingdonationDetail::where('standing_donation_id', '=', $activestand_order->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if (isset($donationdetails)) {
                    $start_date = $donationdetails->instalment_date;
                } else {
                    $start_date = $activestand_order->starting;
                }

                $start_date_carbon = Carbon::parse($start_date);

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

                $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);
                $instalment_date = $instalment_date_carbon->format('Y-m-d');

                Log::info('Standing donation date calculation', [
                    'standing_donation_id' => $activestand_order->id,
                    'user_id'              => $activestand_order->user_id,
                    'start_date'           => $start_date,
                    'interval_raw'         => $intervalValue,
                    'interval_months'      => $intervalMonths,
                    'instalment_date'      => $instalment_date,
                    'current_date'         => $current_date,
                    'payments_type'        => $activestand_order->payments,
                    'payment_made'         => $activestand_order->payment_made,
                    'number_payments'      => $activestand_order->number_payments,
                ]);

                // ============================================================
                // Continuous payments (payments == 2)
                // ============================================================
                if ($activestand_order->payments == 2) {

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
                        Log::warning('Standing donation skipped: insufficient balance', [
                            'user_id'              => $user->id,
                            'standing_donation_id'  => $activestand_order->id,
                            'amount'                => $activestand_order->amount,
                            'available_limit'       => $availableLimit,
                        ]);
                        $this->warn("Skipped standing donation #{$activestand_order->id} — user #{$user->id} insufficient balance.");
                        $donationsSkipped++;
                        continue;
                    }

                    if ($current_date >= $instalment_date) {

                        DB::transaction(function () use ($activestand_order, $instalment_date) {
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
                            $utransaction->amount = $activestand_order->amount;
                            $utransaction->title = "Standing order donation";
                            $utransaction->status = 1;
                            $utransaction->save();

                            $u = User::find($activestand_order->user_id);
                            $u->decrement('balance', $activestand_order->amount);

                            $ch = Charity::find($activestand_order->charity_id);
                            $ch->increment('balance', $activestand_order->amount);
                        });

                        // Email notification (outside transaction)
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
                                    'user_id'              => $activestand_order->user_id,
                                    'email'                => $email,
                                    'error'                => $e->getMessage(),
                                ]);
                            }
                        } else {
                            Log::error('Cannot send email: user or charity not found after transaction', [
                                'standing_donation_id' => $activestand_order->id,
                                'user_id'              => $activestand_order->user_id,
                                'charity_id'           => $activestand_order->charity_id,
                            ]);
                        }

                        $donationsProcessed++;
                        Log::info('Standing donation processed successfully (continuous)', [
                            'standing_donation_id' => $activestand_order->id,
                            'user_id'              => $activestand_order->user_id,
                            'charity_id'           => $activestand_order->charity_id,
                            'amount'               => $activestand_order->amount,
                            'instalment_date'      => $instalment_date,
                        ]);

                    } else {
                        Log::info('Standing donation not yet due (continuous)', [
                            'standing_donation_id' => $activestand_order->id,
                            'instalment_date'       => $instalment_date,
                            'current_date'          => $current_date,
                        ]);
                    }

                // ============================================================
                // Fixed number of payments (payments == 1)
                // ============================================================
                } elseif ($activestand_order->payments == 1) {

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
                        Log::warning('Standing donation skipped: insufficient balance', [
                            'user_id'              => $user->id,
                            'standing_donation_id'  => $activestand_order->id,
                            'amount'                => $activestand_order->amount,
                            'available_limit'       => $availableLimit,
                        ]);
                        $this->warn("Skipped standing donation #{$activestand_order->id} — user #{$user->id} insufficient balance.");
                        $donationsSkipped++;
                        continue;
                    }

                    if (($current_date >= $instalment_date) && ($activestand_order->payment_made < $activestand_order->number_payments)) {

                        DB::transaction(function () use ($activestand_order, $instalment_date) {
                            $doncaldetl = new StandingdonationDetail;
                            $doncaldetl->standing_donation_id = $activestand_order->id;
                            $doncaldetl->user_id = $activestand_order->user_id;
                            $doncaldetl->charity_id = $activestand_order->charity_id;
                            $doncaldetl->amount = $activestand_order->amount;
                            $doncaldetl->instalment_date = $instalment_date;
                            $doncaldetl->instalment_mode = "Fixed";
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

                            $standing_order = StandingDonation::find($activestand_order->id);
                            $standing_order->increment('payment_made', 1);
                        });

                        // Email notification (outside transaction)
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
                                    'user_id'              => $activestand_order->user_id,
                                    'email'                => $email,
                                    'error'                => $e->getMessage(),
                                ]);
                            }
                        } else {
                            Log::error('Cannot send email: user or charity not found after transaction', [
                                'standing_donation_id' => $activestand_order->id,
                                'user_id'              => $activestand_order->user_id,
                                'charity_id'           => $activestand_order->charity_id,
                            ]);
                        }

                        $donationsProcessed++;
                        Log::info('Standing donation processed successfully (fixed)', [
                            'standing_donation_id' => $activestand_order->id,
                            'user_id'              => $activestand_order->user_id,
                            'charity_id'           => $activestand_order->charity_id,
                            'amount'               => $activestand_order->amount,
                            'instalment_date'      => $instalment_date,
                            'payment_made'         => $activestand_order->payment_made + 1,
                            'number_payments'      => $activestand_order->number_payments,
                        ]);

                    } else {
                        Log::info('Standing donation not yet due or all payments completed (fixed)', [
                            'standing_donation_id' => $activestand_order->id,
                            'instalment_date'       => $instalment_date,
                            'current_date'          => $current_date,
                            'payment_made'          => $activestand_order->payment_made,
                            'number_payments'       => $activestand_order->number_payments,
                        ]);
                    }

                // ============================================================
                // Unknown payments type
                // ============================================================
                } else {
                    Log::warning('Unknown payments type, skipping', [
                        'standing_donation_id' => $activestand_order->id,
                        'payments'              => $activestand_order->payments,
                    ]);
                    $donationsSkipped++;
                }

            } catch (\Exception $e) {
                $donationsFailed++;
                Log::error('Failed to process standing donation', [
                    'standing_donation_id' => $activestand_order->id,
                    'user_id'              => $activestand_order->user_id ?? null,
                    'error'                => $e->getMessage(),
                    'trace'                => $e->getTraceAsString(),
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