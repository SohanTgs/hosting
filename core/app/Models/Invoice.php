<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $casts = ['due_date'=>'datetime', 'paid_date'=>'datetime', 'created'=>'datetime'];
    
    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function order(){
        return $this->hasOne(Order::class);
    } 

    public function payment(){
        return $this->belongsTo(Deposit::class, 'deposit_id')->where('status', 1);
    }

    public function trx(){
        return $this->hasOne(Transaction::class, 'invoice_id');
    } 

    public function items(){
        return $this->hasMany(InvoiceItem::class);
    }  
    
    public function scopeCancelled(){ 
        return $this->where('status', 4);
    }
    
    public function scopePaid(){
        return $this->where('status', 1);
    }
    
    public function scopeUnpaid(){
        return $this->where('status', 2);
    }
    
    public function scopePaymentPending(){
        return $this->where('status', 3);
    }
    
    public function scopeRefunded(){
        return $this->where('status', 4);
    }

    public function getShowStatusAttribute(){
      
        if(request()->routeIs('admin*')){
            $class = "badge badge--";
        }else{
            $class = "badge badge-";
        }
        
        $text = 'N/A';

        if($this->status == 1){
            $class .= 'success';
            $text = Self::status()[1];
        }
        elseif($this->status == 2){
            $class .= 'danger';
            $text = Self::status()[2];
        }
        elseif($this->status == 3){
            $class .= 'danger';
            $text = Self::status()[3]; 
        }
        elseif($this->status == 4){
            $class .= 'dark';
            $text = Self::status()[4]; 
        }
        elseif($this->status == 5){
            $class .= 'dark';
            $text = Self::status()[5]; 
        }
     
        return "<span class='$class'>" . trans($text) . "</span>";
    }

    public static function status($implode = false){ 

        $status = [
            1=> trans('Paid'),
            2=> trans('Unpaid'),
            3=> trans('Payment Pending'), 
            4=> trans('Cancelled'),
            5=> trans('Refunded')
        ];

        if($implode){
            return implode(',', array_keys($status));
        }

        return $status;
    }

}
