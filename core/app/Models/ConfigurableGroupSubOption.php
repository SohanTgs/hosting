<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurableGroupSubOption extends Model
{
    use HasFactory; 

    public function group(){
        return $this->belongsTo(ConfigurableGroup::class, 'configurable_group_id', 'id');
    }

    public function price(){
        return $this->hasOne(Pricing::class, 'configurable_group_sub_option_id', 'id');
    }

    public function getOnlyPrice(){
        return $this->price()->select([
                'configurable_group_sub_option_id', 'id', 'monthly_setup_fee', 'quarterly_setup_fee', 'semi_annually_setup_fee', 'annually_setup_fee', 'biennially_setup_fee', 'triennially_setup_fee', 'monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially'
            ]);
    }

}
