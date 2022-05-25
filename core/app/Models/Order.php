<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function hostings(){
        return $this->hasMany(Hosting::class);
    }

    public function domains(){
        return $this->hasMany(Domain::class);
    }

    public function coupon(){
        return $this->belongsTo(Coupon::class);
    }
    
    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
 
    public function scopeActive(){
        return $this->where('status', 1);
    }

    public function scopePending(){
        return $this->where('status', 2);
    }

    public function scopeCancelled(){
        return $this->where('status', 3);
    } 

    public function scopeCancel(){
        return $this->where('status', 3);
    }

    public function getStatusTextAttribute(){

        if(request()->routeIs('admin*')){
            $class = "badge badge--";
        }else{
            $class = "badge badge-";
        }

        $text = 'N/A';
 
        if ($this->status == 0){
            $class .= 'primary';
            $text = Self::status()[0];
        }
        elseif ($this->status == 1){
            $class .= 'danger';
            $text = self::status()[1];
        }
        elseif ($this->status == 2){
            $class .= 'success';
            $text = self::status()[2];
        }
        elseif($this->status == 3){
            $class .= 'warning';
            $text = self::status()[3];
        }
        
        return "<span class='$class'>" . trans($text) . "</span>";
    }

    public static function status(){
        return [
            0=> trans('Initiated'), 
            1=> trans('Pending'), 
            2=> trans('Active'),
            3=> trans('Cancelled'),
        ]; 
    }



}
