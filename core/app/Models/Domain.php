<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory; 

    protected $casts = ['next_invoice_date'=>'date', 'expiry_date'=>'date', 'next_due_date'=>'date', 'reg_time'=>'date']; 

    public function details(){
        return $this->hasOne(InvoiceItem::class, 'relation_id', 'id')->where('type', 4);
    }

    public function deposit(){ 
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }

    public function user(){ 
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getShowStatusAttribute(){

        if(request()->routeIs('admin*')){
            $class = "badge badge--";
        }else{
            $class = "badge badge-";
        }

        if ($this->status == 0){
            $class .= 'dark';
            $text = 'Initiated';
        }
        elseif ($this->status == 1){
            $class .= 'success';
            $text = 'Active';
        }
        elseif ($this->status == 2){
            $class .= 'danger';
            $text = 'Pending';
        }
        
        return "<span class='$class'>" . trans($text) . "</span>";
    }

    public static function status(){
        return [
            1=> trans('Active'),
            2=> trans('Pending')
        ];
    }

}
