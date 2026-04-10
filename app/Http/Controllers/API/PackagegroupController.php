<?php

namespace App\Http\Controllers\API;   

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\Packagegroup;
use App\Http\Resources\Packagegroup as PackagegroupResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PackagegroupController extends BaseController
{
    public function index()
    {
        $enquiery = Packagegroup::all();
        if($enquiery->count() > 0){
            return $this->sendResponse(PackagegroupResource::collection($enquiery), 'Packagegroup retrieved successfully.');
        }else{
            return $this->sendError([], 'Packagegroup not found.');
        }
        

    }
}
