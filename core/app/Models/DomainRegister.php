<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainRegister extends Model
{
    use HasFactory;

    protected $casts = ['params'=>'object'];

    public function scopeActive(){
        return $this->where('status', 1);
    }

    public function scopeDefault(){
        return $this->where('default', 1)->first();
    }

}
