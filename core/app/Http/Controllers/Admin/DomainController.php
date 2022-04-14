<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainSetup;
use App\Models\DomainPricing;
 
class DomainController extends Controller{
    
    public function all(){
        $pageTitle = 'All Domains';
        $domains = DomainSetup::latest()->with('pricing')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.domain.all', compact('pageTitle', 'domains', 'emptyMessage'));
    }

    public function add(Request $request){
       
        $request->validate([
            'extension'=>'required'
        ]);

        $extension = $request->extension;

        if(substr($extension, 0, 1) != '.'){
            $extension = '.'.$extension;
        }
       
        if(DomainSetup::where('extension', $extension)->first()){
            $notify[] = ['error', 'The extension has already been taken'];
            return back()->withNotify($notify);
        } 

        $domain = new DomainSetup();
        $domain->extension = $extension;
        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->save();

        $domainPricing = new DomainPricing();
        $domainPricing->domain_id = $domain->id;
        $domainPricing->save();

        $notify[] = ['success', 'Domain extension added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request){
    
        $request->validate([
            'id'=>'required',
            'extension'=>'required'
        ]);
  
        $extension = $request->extension;

        if(substr($extension, 0, 1) != '.'){
            $extension = '.'.$extension;
        }

        if(DomainSetup::where('extension', $extension)->where('id', '!=', $request->id)->first()){
            $notify[] = ['error', 'The extension has already been taken'];
            return back()->withNotify($notify);
        } 

        $domain = DomainSetup::findOrFail($request->id);
        $domain->extension = $extension;
        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->status = $request->status ? 1 : 0;
        $domain->save();

        $notify[] = ['success', 'Domain extension updated successfully'];
        return back()->withNotify($notify);
    }

    public function updatePricing(Request $request){

        $request->validate([
            'id'=>'required',

            'one_year_price'=>'required|numeric',
            'one_year_id_protection'=>'required|numeric',

            'two_year_price'=>'required|numeric',
            'two_year_id_protection'=>'required|numeric',

            'three_year_price'=>'required|numeric',
            'three_year_id_protection'=>'required|numeric',

            'four_year_price'=>'required|numeric',
            'four_year_id_protection'=>'required|numeric',

            'five_year_price'=>'required|numeric',
            'five_year_id_protection'=>'required|numeric',

            'six_year_price'=>'required|numeric',
            'six_year_id_protection'=>'required|numeric',
        ]);

        $pricing = DomainPricing::findOrFail($request->id);

        $pricing->one_year_price = $request->one_year_price;
        $pricing->one_year_id_protection = $request->one_year_id_protection;

        $pricing->two_year_price = $request->two_year_price;
        $pricing->two_year_id_protection = $request->two_year_id_protection;

        $pricing->three_year_price = $request->three_year_price;
        $pricing->three_year_id_protection = $request->three_year_id_protection;

        $pricing->four_year_price = $request->four_year_price;
        $pricing->four_year_id_protection = $request->four_year_id_protection;

        $pricing->five_year_price = $request->five_year_price;
        $pricing->five_year_id_protection = $request->five_year_id_protection;

        $pricing->six_year_price = $request->six_year_price;
        $pricing->six_year_id_protection = $request->six_year_id_protection;

        $pricing->save();

        $notify[] = ['success', 'Domain pricing updated successfully'];
        return back()->withNotify($notify);
    }

    
}
