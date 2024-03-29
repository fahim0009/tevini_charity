<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    public function charity(){
        return $this->belongsTo('App\Models\Charity');
      }

    public function usertransaction()
    {
      return $this->hasMany('App\Models\Usertransaction');
    }
}
