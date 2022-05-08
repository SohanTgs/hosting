<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancelRequest;
 
class CancelRequestController extends Controller{

    protected function with($with = []){
        $array = ['service.user', 'service.product.serviceCategory'];
        return array_merge($array, $with);
    }

    public function pending(){
        $pageTitle = 'Pending Cancellation Request';
        $cancelRequests = CancelRequest::pending()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.cancel_request.all', compact('pageTitle', 'cancelRequests', 'emptyMessage'));
    }

    public function completed(){
        $pageTitle = 'Completed Cancellation Request';
        $cancelRequests = CancelRequest::completed()->latest()->with($this->with())->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.cancel_request.all', compact('pageTitle', 'cancelRequests', 'emptyMessage'));
    }

    public function cancel(Request $request){

        $request->validate([
            'id'=>'required'
        ]);

        $findCancelRequest = CancelRequest::pending()->findOrFail($request->id);
        $findCancelRequest->status = 1;
        $findCancelRequest->save();

        $service = $findCancelRequest->service;
        $service->domain_status = 4;
        $service->save();
   
        $notify[] = ['success', 'Mark as cancellation successfully'];
        return back()->withNotify($notify);
    }

    public function delete(Request $request){

        $request->validate([
            'id'=>'required'
        ]);

        CancelRequest::findOrFail($request->id)->delete();
   
        $notify[] = ['success', 'Deleted cancellation request successfully'];
        return back()->withNotify($notify);
    }
 
}
