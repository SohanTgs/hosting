<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigurableGroupOption extends Model
{
    use HasFactory;

    public function group(){
        return $this->belongsTo(ConfigurableGroup::class, 'configurable_group_id', 'id');
    }

    public function subOptions(){
        return $this->hasMany(ConfigurableGroupSubOption::class, 'configurable_group_option_id', 'id');
    } 
 
    public function activeSubOptions(){ 
        return $this->subOptions()->where('status', 1);
    } 


}
