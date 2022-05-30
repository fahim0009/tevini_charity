<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charity extends Model
{
    use HasFactory;

    public function donation()
    {
      return $this->hasOne('App\Models\Donation');
    }

    public function transaction()
    {
      return $this->hasOne('App\Models\Transaction');
    }

    public function commission()
    {
      return $this->hasOne('App\Models\Commission');
    }

    public function provoucher()
    {
      return $this->hasOne('App\Models\Provoucher');
    }
    
    public function usertransaction()
    {
      return $this->hasOne('App\Models\Usertransaction');
    }
}
