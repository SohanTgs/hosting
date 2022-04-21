<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model 
{
    use HasFactory;
 
    public function configures() {
        return $this->belongsToMany(ProductConfiguration::class, 'product_configurations', 'product_id', 'configurable_group_id');
    } 
  
    public function getConfigs() {   
        return $this->hasMany(ProductConfiguration::class);
    } 
 
    public function price() {
        return $this->hasOne(Pricing::class);
    }

    public function serverGroup() {
        return $this->belongsTo(ServerGroup::class, 'server_group_id');
    }

    public function serviceCategory() { 
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

}
 