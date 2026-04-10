<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Package as ImageuploadResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Traits\ImageTrait;
use App\Models\Packageimages;
use App\Models\Packagegroup;

class ImageuploadController extends BaseController
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $input = $request->all();
        switch ($request->Input('module')) {
            case 'Package':
                    $imagePrefix = $request->Input('moduleCode').'_'.date('YmdHis').'_'.$request->Input('ImageType');
                    $upload = $this->verifyAndUpload($request, 'Image', "images/package/",$imagePrefix);
                    if($upload){
                        if($request->Input('ImageId') !=''){
                            $Packageimages = Packageimages::where('id', '=',$request->Input('ImageId'))->first();
                            if($Packageimages != null){
                                $Packageimages->Image = $upload;
                                $Packageimages->update();
                                return $this->sendResponse([],' Image updated successfully.');
                            }else
                            {
                                 return $this->sendError(' Image Not Updated');
                            }
                          
                        }
                        else
                        {
                            $data['Image'] = $upload;
                            $data['ImageType'] = $request->Input('ImageType');
                            $data['package_id'] = $request->Input('moduleId');
                            $Packageimages = Packageimages::create($data);
                            return $this->sendResponse([],' Image updated successfully.');
                        }         
                       
                   }else{
                        return $this->sendError(' Image Not Updated');
                   }
                break;

            case 'Packagegroup':
                 $imagePrefix = $request->Input('moduleCode').'_'.date('YmdHis');
                 $upload = $this->verifyAndUpload($request, 'Image', "images/packagegroup/",$imagePrefix);
              
                 if($request->Input('ImageId') !=''){
                    $Packagegroup = Packagegroup::where('id', '=',$request->Input('ImageId'))->first();
                    if($Packagegroup != null){
                        if($upload){
                             $Packagegroup->Image = $upload;
                        }                         
                        $Packagegroup->title = $request->Input('moduleCode');
                        $Packagegroup->subtitle = $request->Input('ImageType');
                        $Packagegroup->slug = $request->Input('moduleId');
                        $Packagegroup->update();
                        return $this->sendResponse([],' Image updated successfully.');
                    }else
                    {
                         return $this->sendError(' Image Not Updated');
                    }
                  
                 }
                 else
                 {
                    if($upload){
                        $data['Image'] = $upload;
                    }                    
                    $data['title'] = $request->Input('moduleCode');
                    $data['subtitle'] = $request->Input('ImageType');
                    $data['slug'] = $request->Input('moduleId');
                    $Packagegroup = Packagegroup::create($data);
                    return $this->sendResponse([],' Image updated successfully.');
                 }         
                       
                   
                break;
            default:
                // code...
                break;
        }

    }
}
