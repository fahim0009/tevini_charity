<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Charity extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    protected $guard = 'charity';

    public function donation()
    {
      return $this->hasOne('App\Models\Donation');
    }

    public function transaction()
    {
      return $this->hasMany('App\Models\Transaction');
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
      return $this->hasMany('App\Models\Usertransaction');
    }

    public function campaign()
    {
      return $this->hasOne('App\Models\Campaign');
    }

    /**
   * Send the password reset notification.
   *
   * @param  string  $token
   * @return void
   */
  public function sendPasswordResetNotification($token)
  {
      $this->notify(new \App\Notifications\CharityResetPasswordNotification($token));
  }




}
