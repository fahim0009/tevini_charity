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
}
