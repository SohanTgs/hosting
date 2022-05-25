<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'amount'];


    public static function status(){ 
        return [
            1=> 'Setup with setup amount',
            2=> 'Item details with amount', 
            3=> 'Coupon Code with amount',
            4=> 'Domain, ID Protection with amount',
            5=> 'Added by admin',
        ];
    }

}
