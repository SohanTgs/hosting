<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'amount'];

    public static function type(){ 
        return [
            1=> 'Setup with setup amount',
            2=> 'Item details with amount', 
            3=> 'Coupon code with amount',
            4=> 'Domain, ID protection with amount',
            5=> 'Added by admin',
            6=> 'Late fee with amount',
        ];
    }

}
