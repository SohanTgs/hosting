<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    
    public function all(){
        $pageTitle = 'All Categories';
        $categories = Category::latest()->paginate(getPaginate());
        $emptyMessage = 'Data not found';
        return view('admin.category.all',compact('pageTitle', 'categories', 'emptyMessage'));
    }

    public function add(Request $request){

    	$request->validate([
    		'name' => 'required|unique:categories|max:255'
    	]);

    	$category = new Category;
    	$category->name = $request->name;
    	$category->description = $request->description;
    	$category->status = $request->status ? 1 : 0;
    	$category->save();

    	$notify[] = ['success', 'Category added successfully'];
	    return back()->withNotify($notify);
    }

    public function update(Request $request){
return $request;
    	$request->validate([
    		'name' => 'required|unique:categories|max:255'
    	]);

    	$category = new Category;
    	$category->name = $request->name;
    	$category->description = $request->description;
    	$category->status = $request->status ? 1 : 0;
    	$category->save();

    	$notify[] = ['success', 'Category added successfully'];
	    return back()->withNotify($notify);
    }

}
