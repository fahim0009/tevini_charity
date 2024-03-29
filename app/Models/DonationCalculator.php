<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCalculator extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo('App\Models\User');
      }

      public function donationdetail()
    {
      return $this->hasMany('App\Models\DonationDetail','donation_cal_id','id');
    }
}
