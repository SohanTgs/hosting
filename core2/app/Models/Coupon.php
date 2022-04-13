<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    public function discount($total){

    	if($this->type == 0){ 
            return ($total * $this->discount) / 100;
    	}
    	else{
            return $this->discount;
    	}

    }

}
