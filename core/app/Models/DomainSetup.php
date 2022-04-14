<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainSetup extends Model
{
    use HasFactory;

    public function scopeActive(){
        return $this->where('status', 1);
    }

    public function pricing(){
        return $this->hasOne(DomainPricing::class, 'domain_id');
    }
 
} 

 