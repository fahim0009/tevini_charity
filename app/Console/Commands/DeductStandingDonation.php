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
    protected $signature = 'donation:deduct-standing';
    protected $description = 'Deduct standing donation amount from donor balance & add to charity balance';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $current_date = now()->format('Y-m-d');
        $current_date_carbon = Carbon::parse($current_date);

        $campaignsProcessed = 0;
        $campaignsFailed = 0;
        $donationsProcessed = 0;
        $donationsSkipped = 0;
        $donationsFailed = 0;

        Log::info('========== DeductStandingDonation command started ==========', [
            'current_date' => $current_date,
        ]);

        // ============================================================
        // Campaign report part (unchanged)
        // ============================================================
        $campaigns = Campaign::where('end_date', '<=', $current_date)
            ->where('emailsend', 0)
            ->get();

        foreach ($campaigns as $campaign) {
            try {
                $charity = Charity::find($campaign->charity_id);
                if (!$charity) {
                    $campaignsFailed++;
                    continue;
                }

                $contactMailModel = ContactMail::where('id', 1)->first();
                if (!$contactMailModel) {
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
            } catch (\Exception $e) {
                $campaignsFailed++;
                continue;
            }
        }

        // ============================================================
        // Standing donation part
        // ============================================================
        $intervalMap = [
            'Monthly'   => 1,
            'Quarterly' => 3,
            'Yearly'    => 12,
            'Annually'  => 12,
            'Weekly'    => 0,  // ✅ FIX: Use days, not months
            'Bi-Weekly' => 0,  // ✅ FIX: Use days, not months
        ];

        $activestand_orders = StandingDonation::where('status', '=', '1')
            ->orderBy('id', 'DESC')
            ->get();

        Log::info('Active standing donations found', [
            'count' => $activestand_orders->count(),
        ]);

        foreach ($activestand_orders as $activestand_order) {
            try {
                // Validate essential fields
                if (!$activestand_order->user_id || !$activestand_order->charity_id || !$activestand_order->amount) {
                    $donationsSkipped++;
                    continue;
                }

                if (!$activestand_order->starting) {
                    $donationsSkipped++;
                    continue;
                }

                // Check if Fixed donation is already completed
                if ($activestand_order->payments == 1 && $activestand_order->payment_made >= $activestand_order->number_payments) {
                    $activestand_order->status = 0;
                    $activestand_order->save();
                    continue;
                }

                // Determine expected instalment date
                $donationdetails = StandingdonationDetail::where('standing_donation_id', '=', $activestand_order->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if (isset($donationdetails)) {
                    $last_date_carbon = Carbon::parse($donationdetails->instalment_date);
                } else {
                    $last_date_carbon = Carbon::parse($activestand_order->starting);
                }

                // ✅ FIX: Resolve interval with proper weekly/bi-weekly support
                $intervalValue = $activestand_order->interval;
                $expected_date = null;

                if (is_string($intervalValue) && $intervalValue === 'Weekly') {
                    $expected_date = $last_date_carbon->copy()->addDays(7);
                } elseif (is_string($intervalValue) && $intervalValue === 'Bi-Weekly') {
                    $expected_date = $last_date_carbon->copy()->addDays(14);
                } elseif (is_string($intervalValue) && isset($intervalMap[$intervalValue])) {
                    $expected_date = $last_date_carbon->copy()->addMonths($intervalMap[$intervalValue]);
                } elseif (is_numeric($intervalValue)) {
                    $expected_date = $last_date_carbon->copy()->addMonths((int) $intervalValue);
                } else {
                    $expected_date = $last_date_carbon->copy()->addMonths(1);
                }

                $expectedDateStr = $expected_date->format('Y-m-d');
                $currentDateStr = $current_date_carbon->format('Y-m-d');

                Log::info('Standing donation date calculation', [
                    'standing_donation_id' => $activestand_order->id,
                    'user_id'              => $activestand_order->user_id,
                    'last_date'            => $last_date_carbon->format('Y-m-d'),
                    'expected_date'        => $expectedDateStr,
                    'current_date'         => $currentDateStr,
                    'interval_value'       => $intervalValue,
                ]);

                // ============================================================
                // ✅ FIX: CRITICAL — Only process on EXACT due date
                // ============================================================
                
                // Case 1: Current date is AFTER expected date (late cron!)
                if ($currentDateStr > $expectedDateStr) {
                    // We missed the due date! Cannot prove balance was sufficient on due date.
                    // Permanently skip.
                    
                    // Check if skip record already exists
                    $alreadyExists = StandingdonationDetail::where('standing_donation_id', $activestand_order->id)
                        ->where('instalment_date', $expectedDateStr)
                        ->exists();
                    
                    if (!$alreadyExists) {
                        DB::transaction(function () use ($activestand_order, $expectedDateStr) {
                            $doncaldetl = new StandingdonationDetail;
                            $doncaldetl->standing_donation_id = $activestand_order->id;
                            $doncaldetl->user_id = $activestand_order->user_id;
                            $doncaldetl->charity_id = $activestand_order->charity_id;
                            $doncaldetl->amount = 0;
                            $doncaldetl->instalment_date = $expectedDateStr;
                            $doncaldetl->instalment_mode = $activestand_order->payments == 1 ? "Fixed" : "continuous";
                            $doncaldetl->status = 2; // Skipped
                            $doncaldetl->save();

                            if ($activestand_order->payments == 1) {
                                $activestand_order->increment('payment_made', 1);
                                if ($activestand_order->payment_made >= $activestand_order->number_payments) {
                                    $activestand_order->status = 0;
                                }
                                $activestand_order->save();
                            }
                        });

                        Log::warning('Standing donation PERMANENTLY SKIPPED - cron was late (past due date)', [
                            'standing_donation_id' => $activestand_order->id,
                            'expected_date'         => $expectedDateStr,
                            'current_date'          => $currentDateStr,
                        ]);
                    }
                    
                    $donationsSkipped++;
                    continue;
                }

                // Case 2: Current date is BEFORE expected date (not due yet)
                if ($currentDateStr < $expectedDateStr) {
                    Log::info('Standing donation not yet due', [
                        'standing_donation_id' => $activestand_order->id,
                        'expected_date'         => $expectedDateStr,
                    ]);
                    continue;
                }

                // Case 3: Current date IS the expected date (due today!)
                // Check if already processed (idempotency)
                $alreadyExists = StandingdonationDetail::where('standing_donation_id', $activestand_order->id)
                    ->where('instalment_date', $expectedDateStr)
                    ->exists();
                
                if ($alreadyExists) {
                    $donationsSkipped++;
                    continue;
                }

                // Due today! Lock user and check balance
                $user = User::find($activestand_order->user_id);
                if (!$user) {
                    $donationsFailed++;
                    continue;
                }

                // ✅ FIX: Process payment with lockForUpdate inside transaction
                DB::transaction(function () use ($activestand_order, $expectedDateStr, $user, &$donationsProcessed) {
                    
                    // ✅ FIX: Lock user row for concurrent safety
                    $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                    $availableLimit = $lockedUser->getAvailableLimit();

                    // Insufficient balance on due date → permanently skip
                    if ($availableLimit < $activestand_order->amount) {
                        Log::warning('Standing donation SKIPPED - insufficient balance ON due date', [
                            'standing_donation_id' => $activestand_order->id,
                            'user_id'              => $lockedUser->id,
                            'available_limit'       => $availableLimit,
                            'amount'                => $activestand_order->amount,
                            'expected_date'         => $expectedDateStr,
                        ]);

                        $doncaldetl = new StandingdonationDetail;
                        $doncaldetl->standing_donation_id = $activestand_order->id;
                        $doncaldetl->user_id = $activestand_order->user_id;
                        $doncaldetl->charity_id = $activestand_order->charity_id;
                        $doncaldetl->amount = 0;
                        $doncaldetl->instalment_date = $expectedDateStr;
                        $doncaldetl->instalment_mode = $activestand_order->payments == 1 ? "Fixed" : "continuous";
                        $doncaldetl->status = 2; // Skipped
                        $doncaldetl->save();

                        if ($activestand_order->payments == 1) {
                            $activestand_order->increment('payment_made', 1);
                            if ($activestand_order->payment_made >= $activestand_order->number_payments) {
                                $activestand_order->status = 0;
                            }
                            $activestand_order->save();
                        }
                        return;
                    }

                    // Sufficient balance! Process payment
                    $doncaldetl = new StandingdonationDetail;
                    $doncaldetl->standing_donation_id = $activestand_order->id;
                    $doncaldetl->user_id = $activestand_order->user_id;
                    $doncaldetl->charity_id = $activestand_order->charity_id;
                    $doncaldetl->amount = $activestand_order->amount;
                    $doncaldetl->instalment_date = $expectedDateStr;
                    $doncaldetl->instalment_mode = $activestand_order->payments == 1 ? "Fixed" : "continuous";
                    $doncaldetl->status = 0;
                    $doncaldetl->save();

                    $utransaction = new Usertransaction();
                    $utransaction->t_id =  "TXN-" . uniqid() . "-" . $activestand_order->user_id;
                    $utransaction->user_id = $activestand_order->user_id;
                    $utransaction->charity_id = $activestand_order->charity_id;
                    $utransaction->standing_donationdetails_id = $activestand_order->id;
                    $utransaction->t_type = "Out";
                    $utransaction->amount = $activestand_order->amount;
                    $utransaction->title = "Standing order donation";
                    $utransaction->status = 1;
                    $utransaction->save();

                    $lockedUser->decrement('balance', $activestand_order->amount);

                    $ch = Charity::find($activestand_order->charity_id);
                    $ch->increment('balance', $activestand_order->amount);

                    if ($activestand_order->payments == 1) {
                        $activestand_order->increment('payment_made', 1);
                        if ($activestand_order->payment_made >= $activestand_order->number_payments) {
                            $activestand_order->status = 0;
                        }
                        $activestand_order->save();
                    }

                    $donationsProcessed++;
                    
                    Log::info('Standing donation processed successfully', [
                        'standing_donation_id' => $activestand_order->id,
                        'amount'               => $activestand_order->amount,
                        'instalment_date'      => $expectedDateStr,
                    ]);
                });

                // Send Email Notification (after commit)
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

            } catch (\Exception $e) {
                $donationsFailed++;
                Log::error('Failed to process standing donation', [
                    'standing_donation_id' => $activestand_order->id,
                    'error'                => $e->getMessage(),
                ]);
                continue;
            }
        }

        Log::info('========== DeductStandingDonation command finished ==========', [
            'campaigns_processed'  => $campaignsProcessed,
            'campaigns_failed'      => $campaignsFailed,
            'donations_processed'   => $donationsProcessed,
            'donations_skipped'     => $donationsSkipped,
            'donations_failed'      => $donationsFailed,
        ]);

        return 0;
    }
}