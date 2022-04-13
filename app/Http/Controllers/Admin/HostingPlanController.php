<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HostingPlanController extends Controller
{
    
    public function all(){
        $pageTitle = 'All Hosting Plans';
        return view('admin.hosting_plan.all',compact('pageTitle'));
    }

    public function newPage(){
        $pageTitle = 'Add Hosting Plan';  
        $maxLimit = str_replace('M','',ini_get('memory_limit')); 
        return view('admin.hosting_plan.add',compact('pageTitle', 'maxLimit'));
    }

    public function add(Request $request){
        return $request;
    }

    public function update(Request $request){
        return $request;
    } 

    public function edit($id){
        return $id;
    }
 
    public function demo(Request $request){


        $ran = rand(100,999);
		$file_name =  $_FILES['file']['name'];
		$tmp_name = $_FILES['file']['tmp_name']; 
		$file_up_name = $ran.'_'.$file_name;

	
        move_uploaded_file($tmp_name, 'uploaded_files/'.$file_up_name);
        return 200;

        $location = imagePath()['profile']['user']['path'];
        $filename = uploadFile($request->file, $location);
    }

}
