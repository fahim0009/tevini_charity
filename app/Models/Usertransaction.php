<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usertransaction extends Model
{
    use HasFactory;
    
           /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'charity_id',
      'user_id',
      'source',
      't_type',
      'amount',
      'note',
      't_unq',
      'title',
      'status',

      'updated_by',
      'created_by',
  ];

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
}
