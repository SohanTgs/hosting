<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hosting extends Model
{
    use HasFactory;

    protected $casts = [
        'config_options' => 'object', 
        'next_due_date'=>'date', 
        'next_invoice_date'=>'date', 
        'suspend_date'=>'date',  
        'termination_date'=>'date',
        'last_update'=>'date', 
        'reg_time'=>'date',
        'last_cron'=>'datetime'
    ];

    public function scopeActive(){
        return $this->where('domain_status', 1);
    }

    public function user(){
        return $this->belongsTo(User::class)->withDefault();
    }

    public function product(){
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function deposit(){ 
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
  
    public function server(){
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function hostingConfigs(){
        return $this->hasMany(HostingConfig::class, 'hosting_id');
    }
 
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cancelRequest(){
        return $this->hasOne(CancelRequest::class, 'hosting_id');
    }

    public function details(){
        return $this->hasOne(InvoiceItem::class, 'relation_id', 'id')->where('type', 2);
    }

    public function getShowDomainStatusAttribute(){

        if(request()->routeIs('admin*')){
            $class = "badge badge--";
        }else{
            $class = "badge badge-";
        }

        $text = 'N/A';

        if ($this->domain_status == 1){
            $class .= 'success';
            $text = Self::domainStatus()[1];
        } 
        if ($this->domain_status == 2){ 
            $class .= 'danger';
            $text = Self::domainStatus()[2];
        }
        elseif ($this->domain_status == 3){
            $class .= 'warning';
            $text = Self::domainStatus()[3];
        }
        elseif ($this->domain_status == 4){
            $class .= 'dark';
            $text = Self::domainStatus()[4];
        }
        elseif ($this->domain_status == 5){
            $class .= 'warning';
            $text = Self::domainStatus()[5];
        }
        
        return "<span class='$class'>" . trans($text) . "</span>";
    }

    public static function domainStatus(){
        return [
            1=> trans('Active'), 
            2=> trans('Pending'),
            3=> trans('Suspended'),
            4=> trans('Terminated'), 
            5=> trans('Cancelled'),
        ]; 
    }

}
