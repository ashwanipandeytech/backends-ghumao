<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait ImageTrait {

    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function verifyAndUpload(Request $request, $fieldname = 'Image', $directory = 'images' ,$imageNamePrefix = '' ) {

        if($request->hasFile($fieldname)) {
                $imageName = $imageNamePrefix.'.'.$request->$fieldname->getClientOriginalExtension();
                if (! File::exists(public_path().'/'.$directory)) {
                        File::makeDirectory(public_path().'/'.$directory,0777,true);
                }
                $request->$fieldname->move($directory, $imageName);
                 
                return $imageName;
        }

        return false;

    }

}