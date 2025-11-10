<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo('App\Models\User');
      }

      public function charity(){
        return $this->belongsTo('App\Models\Charity');
      }

      public function usertransaction(){
        return $this->hasOne('App\Models\Usertransaction');
      }
}
