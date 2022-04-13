<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Domain;

class DomainController extends Controller{
    
    public function all(){
        $pageTitle = 'All Domains';
        $domains = Domain::latest()->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.domain.all', compact('pageTitle', 'domains', 'emptyMessage'));
    }

    public function add(Request $request){

        $request->validate([
            'extension'=>'required',
            'auto_reg'=>'required|in:0',
        ]);

        $extension = $request->extension;

        if(substr($extension, 0, 1) != '.'){
            $extension = '.'.$extension;
        }
       
        if(Domain::where('extension', $extension)->first()){
            $notify[] = ['error', 'The extension has already been taken'];
            return back()->withNotify($notify);
        } 

        $domain = new Domain();
        $domain->extension = $extension;
        $domain->dns_management = $request->dns_management ? 1 : 0;
        $domain->email_forwarding = $request->email_forwarding ? 1: 0;
        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->epp_code = $request->epp_code ? 1 : 0;
        $domain->auto_reg = $request->auto_reg;
        $domain->save();

        $notify[] = ['success', 'Domain extension added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request){
        
        $request->validate([
            'id'=>'required',
            'extension'=>'required',
            'auto_reg'=>'required|in:0',
        ]);
  
        $extension = $request->extension;

        if(substr($extension, 0, 1) != '.'){
            $extension = '.'.$extension;
        }

        if(Domain::where('extension', $extension)->where('id', '!=', $request->id)->first()){
            $notify[] = ['error', 'The extension has already been taken'];
            return back()->withNotify($notify);
        } 

        $domain = Domain::findOrFail($request->id);
        $domain->extension = $extension;
        $domain->dns_management = $request->dns_management ? 1 : 0;
        $domain->email_forwarding = $request->email_forwarding ? 1: 0;
        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->epp_code = $request->epp_code ? 1 : 0;
        $domain->auto_reg = $request->auto_reg;
        $domain->status = $request->status ? 1 : 0;
        $domain->save();

        $notify[] = ['success', 'Domain extension updated successfully'];
        return back()->withNotify($notify);
    }


    
}
