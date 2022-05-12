<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Domain;

class DomainModuleController extends Controller{
    
    public function moduleCommand(Request $request){

        $request->validate([
            'domain_id'=> 'required',
            'module_type'=> 'required|between:1,3'
        ]);

        $domain = Domain::where('status', '!=', 0)->findOrFail($request->domain_id);

        if(!$domain){
            $notify[] = ['error', 'Select server before running the module command'];
            return back()->withNotify($notify);
        }

        if($request->module_type == 1){ 
            return $this->create($domain);
        }

    } 


    protected function create($domain){
        return  $domain;
        $user = $hosting->user;
        $product = $hosting->product; 
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/createacct?api.version=1&username='.$hosting->username.'&domain='.$hosting->domain.'&contactemail='.$user->email.'&password='.$hosting->password.'&pkgname='.$product->package_name);
    
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $notify[] = ['error', @$responseStatus['message']];
                return back()->withNotify($notify);
            }

            $hosting->ns1 = $response->data->nameserver;
            $hosting->ns2 = $response->data->nameserver2;
            $hosting->ns3 = $response->data->nameserver3;
            $hosting->ns4 = $response->data->nameserver4;
            $hosting->package_name = $product->package_name;
            $hosting->ip = $response->data->ip;
            $hosting->save(); 

            $notify[] = ['success', 'Create module command run successfully'];
            return back()->withNotify($notify)->with('response', $response);

        }catch(\Exception  $error){
            $notify[] = ['error', $error->getMessage()];
            return back()->withNotify($notify);
        }

    } 


}
