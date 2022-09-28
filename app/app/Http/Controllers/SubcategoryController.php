<?php

namespace App\Http\Controllers;

use App\Category;
use App\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class SubcategoryController extends Controller
{
    //


    public function __construct()
    {
        $this->category = new SubCategory;
    }
    public function Index($id){
        $cat = SubCategory::where('category_id', decrypt($id))->first();
        return  view('manage.subCategory.index')
        ->with('subcategory', $cat);
    }

    public function create($id){
        return view('manage.subCategory.create')
        ->with('bheading', 'Sub Category')
        ->with('breadcrumb', 'create')
        ->with('category', Category::where('id', decrypt($id))->first());
    }

    public function store(Request $request, $id){
        $validate = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validate->fails()) {
            \Session::flash('alert', 'error');
            \Session::flash('message', 'The fields with * are required');

            return redirect()->back()->withErrors($validate)->withInput($request->all())
                ->with('bheading', 'Category')
                ->with('breadcrumb', 'Index');
        }
        $data = [
            'name' => $request->name,
            'category_id' => decrypt($id)
        ];
        $category =  $this->category->create($data);
        if ($category) {
            \Session::flash('alert', 'success');
            \Session::flash('message', 'Category Added Successfully');
            return redirect()->back()
                ->with('bheading', 'Category')
                ->with('breadcrumb', 'Index');
        }
    }

    public function edit($id){
        return view('manage.subCategory.edit')
        ->with('bheading', 'Sub Category')
        ->with('breadcrumb', 'Edit')
        ->with('category', SubCategory::where('id', decrypt($id))->first());
    }

    public function update(Request $request, $id){
        $data = [
            'name' => $request->name,
        ];
        $category =  SubCategory::where('id', decrypt($id))->first()
                    ->update($data);
        if ($category) {
            \Session::flash('alert', 'success');
            \Session::flash('message', 'Sub Category Updated Successfully');
            return redirect()->back()
                ->with('bheading', 'Sub Category')
                ->with('breadcrumb', 'Edit');
        }
    }
}
