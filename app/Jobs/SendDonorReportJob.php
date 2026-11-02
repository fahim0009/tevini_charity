<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use App\Mail\DonerReport;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendDonorReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $fromDate;
    protected $toDate;
    protected $checkAll;

    public function __construct($userId, $fromDate, $toDate, $checkAll)
    {
        $this->userId = $userId;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        // ✅ FIX: Removed extra $ sign from $this->checkAll
        $this->checkAll = $checkAll;
    }

    public function handle()
    {
        $user = User::find($this->userId);
        if (!$user) return;

        $tamount = Usertransaction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

        // ✅ FIX: Grouped where conditions to prevent fetching other users' data
        $report = Usertransaction::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where(function($q) {
                    $q->whereBetween('created_at', [$this->fromDate, $this->toDate . ' 23:59:59'])
                      ->where('status', 1);
                })->orWhere('pending', 0);
            })
            ->orderBy('id', 'DESC')
            ->get();

        // ✅ FIX: Get balance directly from User Model
        $userBalance = $user->getLiveBalance();

        $contactmail = ContactMail::where('id', 1)->value('name') ?? 'info@tevini.co.uk';

        $pdf = Pdf::loadView('invoices.donor_report', [
            'report' => $report,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'user' => $user,
            'tamount' => $tamount,
        ]);
        $filePath = public_path('/invoices/Report#' . $user->id . '.pdf');
        file_put_contents($filePath, $pdf->output());

        $array = [
            'cc' => $contactmail,
            // ✅ FIX: Use model balance variable
            'userbalance' => number_format($userBalance, 2),
            'name' => $user->name,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'view' => 'mail.donorreport',
            'subject' => 'Monthly statement',
            'from' => 'info@tevini.co.uk',
            'content' => 'Hi, Your donation report has been placed',
            'file' => $filePath,
            'file_name' => 'Report#' . $user->id . '.pdf',
            'subjectsingle' => 'Report Placed - ' . $user->id,
        ];

        if ($this->checkAll == "all") {
            if ($user->email_verified_at) {
                Mail::to($user->email)->send(new DonerReport($array));
            }
        } else {
            Mail::to($user->email)->send(new DonerReport($array));
        }
        
        Log::info("Sending mail to " . $user->email);
    }
}