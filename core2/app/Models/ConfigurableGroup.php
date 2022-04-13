<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class ConfigurableGroup extends Model
{
    use HasFactory;

    public function options(){ 
        return $this->hasMany(ConfigurableGroupOption::class);
    } 
  
    public function products() {
        return $this->belongsToMany(ProductConfiguration::class, 'product_configurations', 'configurable_group_id', 'product_id');
    } 
 
    public function getProducts() {  
        return $this->hasMany(ProductConfiguration::class);
    }
  
    public function activeOptions() {  
        return $this->options()->where('status', 1);
    }


}
 