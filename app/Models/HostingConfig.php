<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingConfig extends Model
{
    use HasFactory;

    public function select(){
        return $this->belongsTo(ConfigurableGroupOption::class, 'configurable_group_option_id')->withDefault();
    }
 
    public function option(){
        return $this->belongsTo(ConfigurableGroupSubOption::class, 'configurable_group_option_id')->withDefault();
    }

}
