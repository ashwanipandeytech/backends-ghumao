<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Package;
use App\Models\Category;
use App\Models\Packageimages;
use Validator;
use App\Http\Resources\Package as PackageResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class PackageController extends BaseController
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
       /* $package = Package::all();
        if($package->count() > 0){
            return $this->sendResponse(PackageResource::collection($package), 'Package retrieved successfully.');
        }else{
            return $this->sendResponse([], 'Package not found.');
        }*/
         if(!empty($request->all())){
           /* $type = json_encode($request->input('Type')[0]);
            $package = DB::select(DB::raw("SELECT * FROM `Package` WHERE JSON_CONTAINS(`Type`,'".$type."')"));*/
            if($request->input('CategoryId') !=''){
                $package = Package::WHERE('CategoryId',$request->input('CategoryId'))->get();
              //  $type = json_encode($request->input('Type')[0]);
              //  $package = DB::select(DB::raw("SELECT * FROM `Package` WHERE CategoryId='".$request->input('CategoryId')."' AND JSON_CONTAINS(`Type`,'".$type."')"));
            }
            else
            {
                $type = json_encode($request->input('Type')[0]);
                $package = DB::select(DB::raw("SELECT * FROM `Package` WHERE JSON_CONTAINS(`Type`,'".$type."')"));

            } 
             if(count($package) > 0){
                foreach ($package as $key => $value) {
                    $catId = $value->CategoryId;
                    $categorySlug=Category::select('CategorySlug')->WHERE('CategoryId',$catId)->pluck('CategorySlug');  
                    $package[$key]->categorySlug = $categorySlug[0];
                    $package[$key]->PakageImages=Packageimages::where('package_id',$value->PackageId)->get();  
                }
                return $this->sendResponse($package, 'Package retrieved successfully.');
            }else{
                return $this->sendResponse([], 'Package not found.');
            }
        }
        else{
           $package = Package::all();
           if($package->count() > 0){
                foreach ($package as $key => $value) {
                    $catId = $value->CategoryId;
                    $categorySlug=Category::select('CategorySlug')->WHERE('CategoryId',$catId)->pluck('CategorySlug');  
                    $package[$key]->categorySlug = $categorySlug[0];
                    $package[$key]->PakageImages=Packageimages::where('package_id',$value->PackageId)->get();  
                }
                return $this->sendResponse(PackageResource::collection($package), 'Package retrieved successfully.');
            }else{
                return $this->sendResponse([], 'Package not found.');
            }
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
            'Code' => 'required|unique:Package',
        ]); 

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if($request->hasFile('Image')) {
                $imageName = $request->Input('Code').'.'.$request->Image->getClientOriginalExtension();
                if (! File::exists(public_path()."/images/package")) {
                        File::makeDirectory(public_path()."/images/package",0777,true);
                }
                $request->Image->move('images/package', $imageName);
               // $package->Image = $imageName;
                $input['Image'] = $imageName;
        }
        else{
            $input['Image'] = '';
        }
     /*   $imageName = $request->Image->getClientOriginalName();
        $request->Image->storeAs('images', $imageName);*/
        $input['Type'] = json_encode($request->Input('Type'));
        $input['Programs'] = json_encode($request->Input('Programs'));
        $input['Inclusions'] = json_encode($request->Input('Inclusions'));
        $input['Exclusions']= json_encode($request->Input('Exclusions'));
        $input['Terms'] = json_encode($request->Input('Terms'));
        $input['Cancellation'] = json_encode($request->Input('Cancellation'));
        $package = Package::create($input);
        $success['PackageId'] =  $package->PackageId;
        return $this->sendResponse( $success,'Package created successfully.');
        
    } 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function details(Request $request)
    {
        $package = Package::find($request->Input('PackageId'));
        if (is_null($package)) {
            return $this->sendError('Package not found.');
        }
        $package->PakageImages=Packageimages::where('package_id',$request->Input('PackageId'))->get(); 
        return $this->sendResponse(new PackageResource($package), 'Package retrieved successfully.');
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
            'PackageName' => 'required',
            'Description' => 'required'
        ]);
      /*  if($request->hasFile('Image')) {
                $imageName =  $request->Input('Code').'.'.$request->Image->getClientOriginalExtension();
                if (! File::exists(public_path()."/images/package/")) {
                        File::makeDirectory(public_path()."/images/package/",0777,true);
                }
                $request->Image->move('images/package', $imageName);
                $input['Image'] = $imageName;
        }*/
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors()); 
        }
        $package = Package::where('PackageId', '=',$request->Input('PackageId'))->first();
        if($package!=null){
            $package->Type = json_encode($input['Type']);
            $package->Title = $input['Title'];
            $package->PackageName = $input['PackageName'];
            $package->Code = $input['Code'];
            $package->PackageSlug = $input['PackageSlug'];
            $package->Programs = json_encode($input['Programs']);
            $package->Cancellation = json_encode($input['Cancellation']);
            $package->NoOfDays = $input['NoOfDays'];
            $package->Description = $input['Description'];
            $package->ShortDescription = $input['ShortDescription'];
          //  $package->Image = $input['Image'];
            $package->Inclusions = json_encode($input['Inclusions']);
            $package->Exclusions = json_encode($input['Exclusions']);
            $package->Terms = json_encode($input['Terms']);
            $package->Price = $input['Price'];
            $package->Priority = $input['Priority'];
            $package->IsActive = $input['IsActive'];
            $package->isMenu = $input['isMenu'];
            $package->Note = $input['Note'];
            $package->NumberOfPerson = $input['NumberOfPerson'];
            $package->CategoryId = $input['CategoryId'];
            $package->update();
            return $this->sendResponse([],'Package updated successfully.');
        }
        else
        {
           return $this->sendResponse([], 'Package not found.');
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
        $catCHeck = Package::where('PackageId', '=',$request->Input('PackageId'))->exists();
        if($catCHeck!=null){
            if(Package::WHERE('PackageId',$request->Input('PackageId'))->delete()){
                return $this->sendResponse([], 'Package deleted successfully.');
            }
        }
        else
        {
           return $this->sendResponse([], 'Package not found.');
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
        $package = Package::where('PackageId', '=',$request->Input('PackageId'))->first();
        if($package!=null){
            $package->IsActive = $input['IsActive'];
            $package->update();
            return $this->sendResponse([],'Package status updated successfully.');
        }
        else
        {
           return $this->sendResponse([], 'Package status not found.');
        }
    }
    
    public function upload(Request $request)
    {
        $input = $request->all();
        $input['Image'] ='';
        $input['InnerImage'] ='';
        $input['SliderImage'] ='';
        if($request->hasFile('Image')) {
                $imageName = $request->Input('Code').'.'.$request->Image->getClientOriginalExtension();
                if (! File::exists(public_path()."/images/package/")) {
                        File::makeDirectory(public_path()."/images/package/",0777,true);
                }
                $request->Image->move('images/package', $imageName);
                $input['Image'] = $imageName;
        }
        if($request->hasFile('InnerImage')) {
                $InnerImageName = $request->Input('Code').'_InnerImage'.'.'.$request->InnerImage->getClientOriginalExtension();
                if (! File::exists(public_path()."/images/package/")) {
                        File::makeDirectory(public_path()."/images/package/",0777,true);
                }
                $request->InnerImage->move('images/package', $InnerImageName);
                $input['InnerImage'] = $InnerImageName;
        }
        if($request->hasFile('SliderImage')) {
                $InnerImageName = $request->Input('Code').'_SliderImage'.'.'.$request->SliderImage->getClientOriginalExtension();
                if (! File::exists(public_path()."/images/package/")) {
                        File::makeDirectory(public_path()."/images/package/",0777,true);
                }
                $request->SliderImage->move('images/package', $InnerImageName);
                $input['SliderImage'] = $InnerImageName;
        }
      
        $package = Package::where('PackageId', '=',$request->Input('PackageId'))->first();
        if($package!=null){
           /* if($input['Image'] !='' && $input['InnerImage'] !=''){
                $package->Image = $input['Image'];
                $package->InnerImage = $input['InnerImage'];
               // $package->SliderImage = $input['SliderImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] !='' && $input['InnerImage'] =='' ){
                $package->Image = $input['Image'];
               // $package->InnerImage = $input['InnerImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] =='' && $input['InnerImage'] !='' ){
                $package->InnerImage = $input['InnerImage'];
               // $package->SliderImage = $input['SliderImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            } else{
                 return $this->sendResponse([],'Package Image updated successfully.');
            }   */
            if($input['Image'] !='' && $input['InnerImage'] !='' && $input['SliderImage'] !=''){
                $package->Image = $input['Image'];
                $package->InnerImage = $input['InnerImage'];
                $package->SliderImage = $input['SliderImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] !='' && $input['InnerImage'] !='' && $input['SliderImage'] ==''){
                $package->Image = $input['Image'];
                $package->InnerImage = $input['InnerImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] !='' && $input['InnerImage'] =='' && $input['SliderImage'] !=''){
                $package->Image = $input['InnerImage'];
                $package->SliderImage = $input['SliderImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] !='' && $input['InnerImage'] =='' && $input['SliderImage'] ==''){
                $package->Image = $input['Image'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] =='' && $input['InnerImage'] !='' && $input['SliderImage'] !=''){
                $package->InnerImage = $input['InnerImage'];
                $package->SliderImage = $input['SliderImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] =='' && $input['InnerImage'] !='' && $input['SliderImage'] ==''){
                $package->InnerImage = $input['InnerImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }elseif($input['Image'] =='' && $input['InnerImage'] =='' && $input['SliderImage'] !=''){
                $package->SliderImage = $input['SliderImage'];
                $package->update();
                return $this->sendResponse([],'Package Image updated successfully.');

            }else{
                 return $this->sendResponse([],'Package Image updated successfully.');
            } 
        }
        else
        {
           return $this->sendResponse([], 'Package not found.');
        }
    }
}
