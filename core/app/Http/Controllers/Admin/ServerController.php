<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\ServerGroup;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ServerController extends Controller{
     
    public function allServer(){
        $pageTitle = 'All Servers';
        $servers = Server::with('group')->paginate(getPaginate());
		$emptyMessage = 'No data found';
        return view('admin.server.all',compact('pageTitle', 'servers', 'emptyMessage'));
    } 
    
    public function addServerPage(){
        $pageTitle = 'Add New Server';
        $groups = ServerGroup::where('status', 1)->latest()->get();
        return view('admin.server.add',compact('pageTitle', 'groups'));
    }
 
    public function addServer(Request $request){

        $request->validate([
    		'name' => 'required|max:255',
    		'hostname' => 'required|url|max:255',
    		'username' => 'required|max:255',
    		'password' => 'required|max:255',
    		'api_token' => 'required',
    		'security_token' => 'required',
    		'server_group_id' => 'required|exists:server_groups,id',
            'ns1' => 'required',
    		'ns1_ip' => 'required',
    		'ns2' => 'required',
    		'ns2_ip' => 'required', 
    	]);
  
        $hostname = $request->hostname;

        if(substr($hostname, -5) != ':2087'){
            $hostname = $hostname.':2087';
        }

        $whmResponse = $this->WHM($request, false, $hostname);
        if(@$whmResponse['error']){
            $notify[] = ['error', @$whmResponse['message']];
            return back()->withNotify($notify);
        }

        $server = new Server();
        $server->type = 'cPanel';
        $server->server_group_id = $request->server_group_id;

        $server->name = $request->name;
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token;
        $server->security_token = $request->security_token;

        $server->ns1 = $request->ns1;
        $server->ns1_ip = $request->ns1_ip;
        $server->ns2 = $request->ns2;
        $server->ns2_ip = $request->ns2_ip;
        $server->ns3 = $request->ns3;
        $server->ns3_ip = $request->ns3_ip;
        $server->ns4 = $request->ns4;
        $server->ns4_ip = $request->ns4_ip;
        $server->ns5 = $request->ns5;
        $server->ns5_ip = $request->ns5_ip;

        $server->ip_address = $this->getIP($request);

        $server->status = 1;
        $server->save();

        $notify[] = ['success', 'Server added successfully'];
	    return redirect()->route('admin.server.edit.page', $server->id)->withNotify($notify);
    }

    public function editServerPage($id){
        $server = Server::findOrFail($id);
        $pageTitle = 'Update Server';
        $groups = ServerGroup::where('status', 1)->latest()->get();
        return view('admin.server.edit',compact('pageTitle', 'groups', 'server'));
    } 

    public function updateServer(Request $request){
     
        $request->validate([
    		'id' => 'required',
    		'name' => 'required|max:255',
    		'hostname' => 'required|url|max:255',
    		'username' => 'required|max:255',
    		'password' => 'required|max:255',
    		'api_token' => 'required',
            'security_token' => 'required',
    		'server_group_id' => 'required|exists:server_groups,id',
            'ns1' => 'required',
    		'ns1_ip' => 'required',
    		'ns2' => 'required',
    		'ns2_ip' => 'required', 
    	]);

        $hostname = $request->hostname;

        if(substr($hostname, -5) != ':2087'){
            $hostname = $hostname.':2087';
        }
     
        $server = Server::findOrFail($request->id);
        $whmResponse = $this->WHM($request, false, $hostname);

        if(@$whmResponse['error']){
            $notify[] = ['error', @$whmResponse['message']];
            return back()->withNotify($notify);
        }

        $server->server_group_id = $request->server_group_id;
        $server->name = $request->name;
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token;
        $server->security_token = $request->security_token;

        $server->ns1 = $request->ns1;
        $server->ns1_ip = $request->ns1_ip;
        $server->ns2 = $request->ns2;
        $server->ns2_ip = $request->ns2_ip;
        $server->ns3 = $request->ns3;
        $server->ns3_ip = $request->ns3_ip;
        $server->ns4 = $request->ns4;
        $server->ns4_ip = $request->ns4_ip;
        $server->ns5 = $request->ns5;
        $server->ns5_ip = $request->ns5_ip;

        $server->ip_address = $request->ip_address;

        $server->status = $request->status ? 1 : 0;
        $server->save();

        $notify[] = ['success', 'Server updated successfully'];
	    return back()->withNotify($notify);
    } 

    public function allGroupServer(){ 

        $pageTitle = 'Server Groups';
		$emptyMessage = 'No data found';

        $servers = Server::where('status', 1)->latest()->get();
        $groups = ServerGroup::paginate(getPaginate());
        
        return view('admin.server.all_group',compact('pageTitle', 'servers', 'emptyMessage', 'groups')); 
    }
 
    public function addGroupServer(Request $request){ 
      
        $request->validate([
    		'name' => 'required|max:255',
    	]);
        
        $group = new ServerGroup();
        $group->name = $request->name;
        $group->save();

        $notify[] = ['success', 'Server group added successfully'];
	    return back()->withNotify($notify);
    }  
 
    public function updateGroupServer(Request $request){

        $request->validate([
    		'id' => 'required',
    		'name' => 'required|max:255',
    	]);
 
        $group = ServerGroup::findOrFail($request->id);
        $group->name = $request->name;
        $group->status = $request->status ? 1 : 0;
        $group->save();

        $notify[] = ['success', 'Server group updated successfully'];
	    return back()->withNotify($notify);
    } 

    public function loginWHM($id){
        $server = Server::findOrFail($id);
        return $this->WHM($server, true);
    } 

    protected function WHM($server, $login, $host = null){

        $username = $server->username;
        $password = $server->password;
        $hostname = $host ?? $server->hostname;

        try{
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode($username.':'.$password),
            ])->get($hostname.'/json-api/create_user_session?api.version=1&user='.$username.'&service=whostmgrd');
    
            $response = json_decode($response);

            if(@$response->cpanelresult->error){
                
                if($login){ 
                    $notify[] = ['error', @$response->cpanelresult->data->reason];
                    return back()->withNotify($notify);
                }
              
                return ['error'=>true, 'message'=>@$response->cpanelresult->data->reason];
            }

            if($login){
                $redirectUrl = $response->data->url;
                return back()->with('url', $redirectUrl);
            }

            return 200;

        }catch(\Exception  $error){
            if($login){ 
                $notify[] = ['error', $error->getMessage()];
                return back()->withNotify($notify);
            }
          
            return ['error'=>true, 'message'=>$error->getMessage()];
        }

    }

    protected function getIP($server){

        try{
            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/accountsummary?api.version=1&user='.$server->username);
        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
        
        $response = json_decode(@$response);
        return @$response->data->acct[0]->ip ?? null;
    }
 
} 

 