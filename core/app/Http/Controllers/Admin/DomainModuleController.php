<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Domain;
use App\DomainRegisters\Register;

class DomainModuleController extends Controller{
    
    public function moduleCommand(Request $request){

        $request->validate([
            'domain_id'=> 'required',
            'module_type'=> 'required|numeric|between:1,6',
        ]);

        $domain = Domain::where('status', '!=', 0)->findOrFail($request->domain_id);

        if(!$domain->domain_register_id){
            $notify[] = ['error', 'Select register before running the module command'];
            return back()->withNotify($notify);
        }

        if($request->module_type == 1){ 
            return $this->register($domain, $request);
        }
        elseif($request->module_type == 2){
            return $this->changeNameservers($domain);
        }
        elseif($request->module_type == 3){
            return $this->renew($domain);
        }
        elseif($request->module_type == 4){
            return $this->setContact($domain, $request);
        }
        elseif($request->module_type == 5){
            return $this->enableIdProtection($domain);
        }
        elseif($request->module_type == 6){
            return $this->disableIdProtection($domain);
        }

    } 

    protected function register($domain, $request){
        $register = new Register($domain->register->alias);
        $register->domain = $domain;
        $register->request = $request;
        $register->command = 'register';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'Register module command run successfully'];
        return back()->withNotify($notify);
    } 

    protected function renew($domain){ 
        $register = new Register($domain->register->alias);
        $register->domain = $domain;
        $register->command = 'renew';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Renew module command run successfully for '.$domain->reg_period.' year'];
        return back()->withNotify($notify);
    }

    public function setContact($domain, $request){
        $register = new Register($domain->register->alias);
        $register->domain = $domain;
        $register->request = $request;
        $register->command = 'setContact';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'The changes to the domain were saved successfully'];
        return back()->withNotify($notify);
    }

    protected function changeNameservers($domain){
        $register = new Register($domain->register->alias);
        $register->domain = $domain;
        $register->command = 'changeNameservers';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'Change nameservers module command run successfully'];
        return back()->withNotify($notify);
    }

    protected function enableIdProtection($domain){
        $register = new Register($domain->register->alias);
        $register->domain = $domain;
        $register->command = 'enableIdProtection';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'Domain privacy has been enabled'];
        return back()->withNotify($notify);
    }

    protected function disableIdProtection($domain){
        $register = new Register($domain->register->alias);
        $register->domain = $domain;
        $register->command = 'disableIdProtection';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'Domain privacy has been disabled'];
        return back()->withNotify($notify);
    }


}
