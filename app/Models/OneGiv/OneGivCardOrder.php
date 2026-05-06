<?php
namespace App\Models\OneGiv;
use Illuminate\Database\Eloquent\Model;

class OneGivCardOrder extends Model
{
    protected $fillable = [
        'date',
        'accountno',
        'user_id',
        'order_id',
        'order_number',
        'card_holder',
        'fixed_amount',
        'amount',
        'pin',
        'email',        
        'mobile',       
        'house_number', 
        'street',       
        'address2',     
        'city',         
        'postcode',     
        'country',      
        'extra1',
        'extra2',
        'status',
        'updated_by',
        'created_by',
    ];
}