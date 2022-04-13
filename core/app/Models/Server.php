<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    public function group(){
        return $this->belongsTo(ServerGroup::class, 'server_group_id');
    }

}
