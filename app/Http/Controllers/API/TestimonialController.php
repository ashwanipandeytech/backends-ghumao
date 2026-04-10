<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Testimonial;
use Validator;
use App\Http\Resources\Testimonial as TestimonialResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class TestimonialController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $testimonial = Testimonial::all();
        if($testimonial->count() > 0){
            return $this->sendResponse(TestimonialResource::collection($testimonial), 'Testimonial retrieved successfully.');
        }else{
            return $this->sendResponse([], 'Testimonial not found.');
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
            'Name' => 'required',
            'Image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'Testimonial' => 'required',
        ]); 

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
   
        $testimonial = Testimonial::create($input);
        $lastInsertId=$testimonial->id;
         if($request->hasFile('Image')) {
                $imageName = $lastInsertId."_".$request->Image->getClientOriginalName();
                 if (! File::exists(public_path()."/images/testimonial")) {
                        File::makeDirectory(public_path()."/images/testimonial",0777,true);
                }
                $request->Image->move('images/testimonial', $imageName);
                $input['Image'] = $imageName;
                $testimonialupdate = Testimonial::where('id', '=',$lastInsertId)->first();
                if($testimonialupdate!=null){
                    $testimonialupdate->Image = $input['Image'];
                    $testimonialupdate->update();
                }
        }
        return $this->sendResponse('','Testimonial created successfully.');
        
    } 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function details(Request $request)
    {
        $testimonial = Testimonial::find($request->Input('id'));
        if (is_null($testimonial)) {
            return $this->sendError('Testimonial not found.');
        }
        return $this->sendResponse(new TestimonialResource($testimonial), 'Testimonial retrieved successfully.');
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
            'Name' => 'required',
            'Testimonial' => 'required'
        ]);
        if($request->hasFile('Image')) {
                $imageName =  $request->Input('id').'_'.$request->Image->getClientOriginalName();
                if (! File::exists(public_path()."/images/testimonial/")) {
                        File::makeDirectory(public_path()."/images/testimonial/",0777,true);
                }
                $request->Image->move('images/testimonial', $imageName);
                $input['Image'] = $imageName;
        }
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors()); 
        }
        $testimonial = Testimonial::where('id', '=',$request->Input('id'))->first();
        if($testimonial!=null){
            $testimonial->Name = $input['Name'];
            $testimonial->Testimonial = $input['Testimonial'];
            $testimonial->Address  = $input[' Address '];
            $testimonial->Image = $input['Image'];
            $testimonial->update();
            return $this->sendResponse([],'Testimonial updated successfully.');
        }
        else
        {
           return $this->sendResponse([], 'Testimonial not found.');
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
        $catCHeck = Testimonial::where('id', '=',$request->Input('id'))->exists();
        if($catCHeck!=null){
            if(Testimonial::WHERE('id',$request->Input('id'))->delete()){
                return $this->sendResponse([], 'Testimonial deleted successfully.');
            }
        }
        else
        {
           return $this->sendResponse([], 'Testimonial not found.');
        }
    }
}
