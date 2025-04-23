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

class SendDonorReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $fromDate;
    protected $toDate;

    public function __construct($userId, $fromDate, $toDate)
    {
        $this->userId = $userId;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function handle()
    {
        $user = User::find($this->userId);
        if (!$user) return;

        $tamount = Usertransaction::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();

        $report = Usertransaction::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereBetween('created_at', [$this->fromDate, $this->toDate . ' 23:59:59'])
                    ->where('status', 1);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('pending', 0);
            })
            ->orderBy('id', 'DESC')
            ->get();

        $userTransactionBalance = Usertransaction::selectRaw('
            SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
            SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
        ')
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->orWhere(function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('pending', 1);
            })
            ->first();

        $contactmail = ContactMail::where('id', 1)->value('name');

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
            'userbalance' => number_format($userTransactionBalance->balance, 2),
            'name' => $user->name,
            'view' => 'mail.donorreport',
            'subject' => 'Monthly statement',
            'from' => 'info@tevini.co.uk',
            'content' => 'Hi, Your donation report has been placed',
            'file' => $filePath,
            'file_name' => 'Report#' . $user->id . '.pdf',
            'subjectsingle' => 'Report Placed - ' . $user->id,
        ];

        if ($user->email_verified_at) {
            // Mail::to($user->email)->queue(new DonerReport($array));
            Mail::to($user->email)->send(new DonerReport($array));

        }

        \Log::info("Sending mail to " . $user->email);

    }

}
