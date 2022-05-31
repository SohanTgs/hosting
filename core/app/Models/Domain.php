<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory; 

    protected $casts = ['next_invoice_date'=>'date', 'expiry_date'=>'date', 'next_due_date'=>'date', 'reg_time'=>'date', 'last_cron'=>'datetime']; 

    public function scopeActive(){
        return $this->where('status', 1);
    }

    public function details(){
        return $this->hasOne(InvoiceItem::class, 'relation_id', 'id')->where('type', 4);
    }

    public function deposit(){ 
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }

    public function hosting(){ 
        return $this->belongsTo(Hosting::class, 'hosting_id');
    }

    public function invoices(){
        return $this->hasMany(Invoice::class, 'domain_id');
    }

    public function user(){ 
        return $this->belongsTo(User::class, 'user_id');
    }

    public function register(){ 
        return $this->belongsTo(DomainRegister::class, 'domain_register_id');
    }

    public function getShowStatusAttribute(){

        if(request()->routeIs('admin*')){
            $class = "badge badge--";
        }else{
            $class = "badge badge-";
        } 

        $text = 'N/A'; 

        if ($this->status == 1){
            $class .= 'success';
            $text = Self::status()[1];
        }
        elseif ($this->status == 2){
            $class .= 'danger';
            $text = Self::status()[2];
        }
        elseif ($this->status == 3){
            $class .= 'warning';
            $text = Self::status()[3];
        }
        elseif ($this->status == 4){
            $class .= 'danger';
            $text = Self::status()[4];
        }
        elseif ($this->status == 5){
            $class .= 'warning';
            $text = Self::status()[5];
        }
        
        return "<span class='$class'>" . trans($text) . "</span>";
    }

    public static function status(){ 
        return [
            1=> trans('Active'),
            2=> trans('Pending'), 
            3=> trans('Pending Registration'),
            4=> trans('Expired'), 
            5=> trans('Cancelled'),
        ];
    }

}

