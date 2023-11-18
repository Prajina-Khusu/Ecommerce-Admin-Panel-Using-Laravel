<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subcategories =SubCategory::select('sub_categories.*','categories.name as categoryName')->latest('sub_categories.id')->leftJoin('categories','categories.id','sub_categories.category_id');
        if(!empty($request->get('keyword')))
        {
            $subcategories =$subcategories->where('sub_categories.name','like','%'.$request->get('keyword').'%');
            $subcategories =$subcategories->orwhere('categories.name','like','%'.$request->get('keyword').'%');

        }
        $subcategories = $subcategories->paginate(8);
        return view('admin.sub_category.list',compact('subcategories'));

    }
    public function create()
    {
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        return view('admin.sub_category.create',$data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:sub_categories',
            'category'=>'required',
            'status'=>'required'
        ]);

        if($validator->passes())
        {
            $subcategory= new SubCategory();
            $subcategory->name=$request->name;
            $subcategory->slug=$request->slug;
            $subcategory->status=$request->status;
            $subcategory->showHome=$request->showHome;
            $subcategory->category_id=$request->category;
            $subcategory->save();
            
            $request->session()->flash('success','Categories created successfully');

            return response([
                'status'=>true,
                'message'=>'Sub Category created successfully'
            ]);




        }
        else{
            return response([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $subcategory=SubCategory::find($id);
        if(empty($subcategory))
        {
            $request->session()->flash('error','Record not found');
            return redirect()->route('sub-categories.index');
        }
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['subcategory']=$subcategory;
        return view('admin.sub_category.edit',$data);

    }

    public function update($id,Request $request)
    {
        $subcategory=SubCategory::find($id);
        if(empty($subcategory))
        {
            $request->session()->flash('error','Record not found');
            return response([
                'status'=>false,
                'notFound'=>true
            ]);
           // return redirect()->route('sub-categories.index');
        }

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:sub_categories,slug,'.$subcategory->id.',id',
            'category'=>'required',
            'status'=>'required'
        ]);

        if($validator->passes())
        {
            $subcategory->name=$request->name;
            $subcategory->slug=$request->slug;
            $subcategory->status=$request->status;
            $subcategory->showHome=$request->showHome;
            $subcategory->category_id=$request->category;
            $subcategory->save();
            
            $request->session()->flash('success','Categories updated successfully');

            return response([
                'status'=>true,
                'message'=>'Sub Category updated successfully'
            ]);




        }
        else{
            return response([
                'status'=>false,
                'errors'=>$validator->errors()
            ]);
        }

    }
    public function destroy($id,Request $request)
    {

        $subcategory = SubCategory::find($id);
        if(empty($subcategory))
        {
            $request->session()->flash('error','Record not found');
            return response([
                'status'=>false,
                'notFound'=>true
            ]);
        }
        $subcategory->delete();


        $request->session()->flash('success','Sub Categories deleted successfully');

        return response([
            'status'=>true,
            'message'=>'Sucessfully deleted '
        ]);
        return redirect()->route('sub-categories.index');


        
    }
}
