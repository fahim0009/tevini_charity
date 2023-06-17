<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'is_type',
        'password',
        'is_admin',
        'address',

        'title',
        'surname',
        'houseno',
        'street',
        'town',
        'postcode',
        'accountno',
        'balance',

        'country',
        'city',
        'phone',
        'status',
        'about',
        'facebook',
        'twitter',
        'google',
        'linkedin',
        'notification',
        'updated_by',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function role()
    // {
    //     return $this->belongsTo('App\Models\Role');
    // }

    public function staff()
    {
      return $this->hasOne('App\Models\Staff');
    }

    public function order()
    {
      return $this->hasOne('App\Models\Order');
    }

    public function barcode()
    {
      return $this->hasOne('App\Models\Barcode');
    }

    public function donation()
    {
      return $this->hasOne('App\Models\Donation');
    }

    public function commission()
    {
      return $this->hasOne('App\Models\Commission');
    }

    public function transaction()
    {
      return $this->hasOne('App\Models\Transaction');
    }

    public function provoucher()
    {
      return $this->hasOne('App\Models\Provoucher');
    }

    public function usertransaction()
    {
      return $this->hasOne('App\Models\Usertransaction');
    }

    public function stripetopup()
    {
      return $this->hasOne('App\Models\StripeTopup');
    }

    public function donationdetail()
    {
      return $this->hasOne('App\Models\DonationDetail');
    }

    public function donationcalculator()
    {
      return $this->hasOne('App\Models\DonationCalculator');
    }

    public function standingdonationdetail()
    {
      return $this->hasOne('App\Models\StandingdonationDetail');
    }

    public function standingdonation()
    {
      return $this->hasOne('App\Models\StandingDonation');
    }


}
