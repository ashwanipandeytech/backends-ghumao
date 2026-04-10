<?php

namespace App\Http\Controllers\API;   

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Category;
use App\Models\Package;
use Validator;
use App\Http\Resources\Category as CategoryResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
       // $category = Category::all();
        $category = Category::whereNull('parent_id')->WHERE('isMenu','Yes')->WHERE('IsActive','Yes')->get();
        if($category->count() > 0){
             foreach ($category as $key => $value) {
                // if($value['isMenu'] == 'Yes'){
                //   // $category[$key]['Packages']=Package::select('PackageSlug')->WHERE('CategoryId',$value['CategoryId'])->get();
                //     $category[$key]['Packages']=Package::select('PackageSlug','PackageName')->WHERE('CategoryId',$value['CategoryId'])->WHERE('isMenu','Yes')->get();
                // }  
                  if($value['isMenu'] == 'Yes' && $value['CategoryId']=='1'){
                    $category[$key]['Packages']=Package::select('PackageSlug','PackageName')->whereIn('PackageId',array(1,12))->WHERE('isMenu','Yes')->get();
                }    
            }
            return $this->sendResponse(CategoryResource::collection($category), 'Category retrieved successfully.');
        }else{
            return $this->sendResponse([], 'Catgeory not found.');
        }
        

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function add(Request $request)
    {
      
        $input = $request->all();
         
        $validator = Validator::make($input, [
            'CategoryName' => 'required|unique:Categories',
            'CategorySlug' => 'required|unique:Categories',
            'Image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]); 

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

      //  $imageName = $request->Image->getClientOriginalName().'.'.$request->Image->extension(); 
      /*  if($request->hasFile('Image')) {
                $imageName = $request->Image->getClientOriginalName();
                if (! File::exists(public_path()."/images/category")) {
                        File::makeDirectory(public_path()."/images/category",0777,true);
                }
                $request->Image->move('images/category', $imageName);
                //$category->Image = $imageName;
                $input['Image'] = $imageName;
        }
        else{
            $input['Image'] = '';
        }*/
     /*   $imageName = $request->Image->getClientOriginalName();
        $request->Image->storeAs('images', $imageName);*/
        
        $category = Category::create($input);
         $lastInsertId=$category->CategoryId;
        if($request->hasFile('Image')) {
                $imageName = $lastInsertId."_".$request->Image->getClientOriginalName();
                if (! File::exists(public_path()."/images/category")) {
                        File::makeDirectory(public_path()."/images/category",0777,true);
                }
                $request->Image->move('images/category', $imageName);
                //$category->Image = $imageName;
                $input['Image'] = $imageName;
                $category = Category::where('CategoryId', '=',$lastInsertId)->first();
                if($category!=null){
                    $category->Image = $input['Image'];
                    $category->update();
                }
        }
        return $this->sendResponse('','Category created successfully.');
        
    } 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function details(Request $request)
    {
        $category = Category::find($request->Input('CategoryId'));
        if (is_null($category)) {
            return $this->sendError('Category not found.');
        }
        return $this->sendResponse(new CategoryResource($category), 'Category retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'CategoryName' => 'required',
            'Description' => 'required'
        ]);
        if($request->hasFile('Image')) {
                 $imageName = $request->Input('CategoryId').'_'.$request->Image->getClientOriginalName();
                if (! File::exists(public_path()."/images/category/")) {
                        File::makeDirectory(public_path()."/images/category/",0777,true);
                }
                $request->Image->move('images/category', $imageName);
                $input['Image'] = $imageName;
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors()); 
        }
        $category = Category::where('CategoryId', '=',$request->Input('CategoryId'))->first();
        if($category!=null){
            $category->CategoryName = $input['CategoryName'];
            $category->Description = $input['Description'];
            $category->isMenu = $input['isMenu'];
            $category->IsActive = $input['IsActive'];
            $category->StartingPrice = $input['StartingPrice'];
          //  $category->Image = $input['Image'];
            $category->CategorySlug = $input['CategorySlug'];
            $category->CategoryTitle = $input['CategoryTitle'];
            $category->parent_id = $input['parent_id'];
            $category->Cities = empty($input['Cities'])?'':$input['Cities'];
            if($request->hasFile('Image')) {
                 $category->Image = $input['Image'];
            }
            $category->update();
            return $this->sendResponse([],'Category updated successfully.');
        }
        else
        {
           return $this->sendResponse([], 'Category not found.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function remove(Request $request)
    {
        //$catCHeck = Category::findOrFail($request->Input('CategoryId'));
        $catCHeck = Category::where('CategoryId', '=',$request->Input('CategoryId'))->exists();
        if($catCHeck!=null){
            if(Category::WHERE('CategoryId',$request->Input('CategoryId'))->delete()){
                return $this->sendResponse([], 'Category deleted successfully.');
            }
        }
        else
        {
           return $this->sendResponse([], 'Category not found.');
        }
    }
    
     /**
     * Update the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */

    public function statusupdate(Request $request)
    {
       
        $input = $request->all();
        $category = Category::where('CategoryId', '=',$request->Input('CategoryId'))->first();
        if($category!=null){
            $category->IsActive = $input['IsActive'];
            $category->update();
            return $this->sendResponse([],'Category status updated successfully.');
        }
        else
        {
           return $this->sendResponse([], 'Category status not found.');
        }
    }
    
    public function list()
    {
        //$category = Category::all();
        $category = Category::with('childrenRecursive')->whereNull('parent_id')->get();
        if($category->count() > 0){
           /* foreach ($category as $key => $value) {
                if($value['isMenu'] == 'Yes'){
                    $category[$key]['Packages']=Package::select('PackageSlug','PackageName')->WHERE('CategoryId',$value['CategoryId'])->WHERE('isMenu','Yes')->get();
                }               
            }*/
            return $this->sendResponse(CategoryResource::collection($category), 'Category retrieved successfully.');
        }else{
            return $this->sendError([], 'Catgeory not found.');
        }
        
    }
}