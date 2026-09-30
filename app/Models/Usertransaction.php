<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Usertransaction extends Model
{
    use HasFactory;
    
           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\Models\User');
      }

      public function charity(){
        return $this->belongsTo('App\Models\Charity');
      }

      public function campaign(){
        return $this->belongsTo('App\Models\Campaign');
      }

      public function standingdonationDetail(){
        return $this->belongsTo('App\Models\StandingdonationDetail', 'standing_donationdetails_id');
      }

      public function standingDonations(){
        return $this->belongsTo('App\Models\StandingDonation', 'standing_donationdetails_id');
      }


      public function donation(){
        return $this->belongsTo('App\Models\Donation', 'donation_id');
      }

      public function standingDonation()
      {
        return $this->hasOneThrough(
          'App\Models\StandingDonation',
          'App\Models\StandingdonationDetail',
          'id', // Foreign key on StandingdonationDetail table...
          'id', // Foreign key on StandingDonation table...
          'standing_donationdetails_id', // Local key on Usertransaction table...
          'standing_donation_id' // Local key on StandingdonationDetail table...
        );
      }


      public function provoucher()
      {
          return $this->hasOne(Provoucher::class, 'tran_id', 'id');
      }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            // Automatically set business_date when a new transaction is created
            if (empty($transaction->business_date)) {
                $transaction->business_date = self::calculateBusinessDate($transaction->created_at);
            }
        });
    }

    public static function calculateBusinessDate($createdAt)
    {
        $date = Carbon::parse($createdAt);
        
        // Find the cutoff time that was active on the transaction's creation date
        $history = DB::table('cutoff_histories')
            ->where('effective_date', '<=', $date->toDateString())
            ->orderBy('effective_date', 'desc')
            ->first();

        // Use found time or fallback to default 16:30
        $cutoffTime = $history ? $history->cutoff_time : '16:30';
        $cutoffDateTime = $date->copy()->setTimeFromTimeString($cutoffTime);

        // If transaction time is after cutoff, move to the next day
        if ($date->gte($cutoffDateTime)) {
            $date->addDay();
        }

        // Weekend Logic: Shift Friday, Saturday, Sunday to Monday
        if ($date->isFriday()) {
            $date->addDays(3); 
        } else if ($date->isSaturday()) {
            $date->addDays(2); 
        } else if ($date->isSunday()) {
            $date->addDay();   
        }

        return $date->toDateString();
    }


}
