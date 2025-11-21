<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_type',
        'prefix_name',
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
        'passwordchk',
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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

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

    public function accDelRequest()
    {
        return $this->hasMany(AccDelRequest::class);
    }

    public function getLiveBalance()
    {
        return Usertransaction::where('user_id', $this->id)
            ->where(function ($q) {
                $q->where('status', 1)
                  ->orWhere('pending', 1);
            })
            ->selectRaw('
                COALESCE(
                    SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                    SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END),
                0) as balance
            ')
            ->value('balance');
    }

    public function getAvailableLimit()
    {
        return $this->getLiveBalance() + $this->overdrawn_amount;
    }



}
