<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCategory;
use App\Models\Server;
use App\Models\ServerGroup;

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
    		'hostname' => 'required|max:255',
    		'username' => 'required|max:255',
    		'password' => 'required|max:255',
    		'api_token' => 'required',
    		'server_group_id' => 'required|exists:server_groups,id',
    	]);
    
        $server = new Server();
        $server->type = 'cPanel';
        $server->server_group_id = $request->server_group_id;

        $server->name = $request->name;
        $server->hostname = $request->hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token;
        $server->save();

        $notify[] = ['success', 'Server added successfully'];
	    return back()->withNotify($notify);
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
    		'hostname' => 'required|max:255',
    		'username' => 'required|max:255',
    		'password' => 'required|max:255',
    		'api_token' => 'required',
    		'server_group_id' => 'required|exists:server_groups,id',
    	]);
      
        $server = Server::findOrFail($request->id);
        $server->server_group_id = $request->server_group_id;

        $server->name = $request->name;
        $server->hostname = $request->hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token;

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

 
    

} 

 