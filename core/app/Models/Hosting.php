<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hosting extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['config_options' => 'object', 'reg_time'=>'date', 'termination_date'=>'date', 'suspend_date'=>'date'];

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

    public function details(){
        return $this->hasOne(InvoiceItem::class, 'relation_id', 'id')->where('type', 2);
    }

    public function getShowDomainStatusAttribute(){

        if(request()->routeIs('admin*')){
            $class = "badge badge--";
        }else{
            $class = "badge badge-";
        }

        if ($this->domain_status == 1){
            $class .= 'primary';
            $text = 'Active';
        }
        elseif ($this->domain_status == 2){
            $class .= 'danger';
            $text = 'Pending';
        }
        elseif ($this->domain_status == 3){
            $class .= 'success';
            $text = 'Completed'; 
        }
        
        return "<span class='$class'>" . trans($text) . "</span>";
    }

    public static function domainStatus(){
        return [
            1=> trans('Active'),
            2=> trans('Pending'),
            3=> trans('Completed'),
        ];
    }

}
