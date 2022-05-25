<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainRegister;

class DomainRegisterController extends Controller{
     

    public function all(){
        $pageTitle = 'Domain Registers';
        $domainRegisters = DomainRegister::latest()->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.domain.register', compact('pageTitle', 'domainRegisters', 'emptyMessage'));
    }

    public function changeStatus(Request $request){

        $request->validate([
            'id'=>'required'
        ]);

        $register = DomainRegister::findOrFail($request->id);

        if($register->status == 1){
            $register->status = 0;
            $message = $register->name.' has been disabled';
        }else{
            $register->status = 1;
            $message = $register->name.' has been enabled';
        }

        $register->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function update(Request $request){

        $register = DomainRegister::findOrFail($request->id);
        $data = (array) $register->params;
        $arrayFields = $data;

        array_walk($arrayFields, function(&$field) use ($request){
       
            if($request->input('test_mode')){
                if(@$field->test_mode || @$field->required){
                    $field = 'required'; 
                }
                else{
                    $field = 'nullable';
                }
            }else{
                if(!@$field->test_mode || @$field->required){
                    $field = 'required'; 
                }
                else{
                    $field = 'nullable';
                }
            }
            
        });

        $request->validate($arrayFields);
   
        array_walk($data, function(&$field, $value) use ($request){
            $field->value = $request->input($value) ?? ""; 
        });

        $register->test_mode = $request->test_mode ? 1 : 0;
        $register->default = $request->default ? 1 : 0;
        $register->status = $request->status ? 1 : 0;
        $register->params = $data;
        $register->save();

        if($request->default){
            DomainRegister::where('id', '!=', $request->id)->where('default', 1)->update(['default'=>0]);
        }

        $notify[] = ['success', $register->name.' has been updated'];
        return back()->withNotify($notify);
    }

}





