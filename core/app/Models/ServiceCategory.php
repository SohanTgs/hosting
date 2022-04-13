<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{  
    use HasFactory;

    public function products($filter = false){ 

        $with = ['getConfigs']; 

        if($filter){
            array_push($with, 'price'); 
        }

        return $this->hasMany(Product::class, 'category_id')
                    ->when($filter, function($query){  
                        $query->where('status', 1)
                              ->whereHas('price', function($price){
                            $price->filter($price); 
                        });  
                    })->with($with);
    }
    

}


  