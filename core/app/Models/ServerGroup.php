<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerGroup extends Model
{
    use HasFactory; 

    public function servers(){
        return $this->hasMany(Server::class, 'server_group_id');
    }

} 
