<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $casts = ['date'=>'datetime', 'due_date'=>'datetime', 'paid_date'=>'datetime'];
    
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
    

    public function scopePaid(){
        return $this->where('status', 1);
    }

    public function scopeUnpaid(){
        return $this->where('status', 0);
    }

    public function getStatusTextAttribute(){
      
        if(request()->routeIs('admin*')){
            $class = "badge badge--";
        }else{
            $class = "badge badge-";
        }

        if($this->status == 1){
            $class .= 'success';
            $text = 'Paid';
        }elseif($this->status == 0 && !$this->payment){
            $class .= 'dark';
            $text = 'Initiated'; 
        }
        elseif($this->status == 0 && $this->payment &&  $this->payment->status == 0){
            $class .= 'danger';
            $text = 'Unpaid';
        }
     
        return "<span class='$class'>" . trans($text) . "</span>";
    }

}
