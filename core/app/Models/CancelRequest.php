<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelRequest extends Model
{
    use HasFactory;

    public function service(){
        return $this->belongsTo(Hosting::class, 'hosting_id');
    }

    public function scopePending(){
        return $this->where('status', 2);
    }

    public function scopeCompleted(){
        return $this->where('status', 1);
    }

    public function getShowStatusAttribute(){

        if ($this->status == 1){
            $class = 'badge badge--primary';
            $text = 'Completed';
        } 
        if ($this->status == 2){ 
            $class = 'badge badge--danger';
            $text = 'Pending';
        }
        
        return "<span class='$class'>" . trans($text) . "</span>";
    }

    public static function status(){
        return [
            1=> trans('Completed'),
            2=> trans('Pending')
        ]; 
    }

    public static function type(){ 
        return [
            1=> trans('Immediate'),
            2=> trans('End of Billing Period')
        ]; 
    }


}
