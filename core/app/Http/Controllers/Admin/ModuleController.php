<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting;
use Illuminate\Support\Facades\Http;


class ModuleController extends Controller{
    
    public function moduleCommand(Request $request){

        $request->validate([
            'hosting_id'=> 'required',
            'module_type'=> 'required|between:1,6',
        ]);

        $hosting = Hosting::where('status', 1)->findOrFail($request->hosting_id);

        if($request->module_type == 1){ 
            return $this->createCommand();
        }

        return $hosting;
    } 

    protected function createCommand(){

        
        $response = Http::get('https://whmcs.viserlab.com:2087/cpsess##########/json-api/createacct?api.version=1&username=username&domain=example.com');

        // P4=*5l-IO1R2

        return json_decode($response);

        return 'createCommand';
    }

}
