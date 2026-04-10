<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource
{
 
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return [

        //     'CategoryId' => $this->CategoryId,
        //     'CategoryName' => $this->CategoryName,
        //     'Description' => $this->Description,
        //     'Image' => $this->Image,
        //     'isMenu' => $this->isMenu,
        //     'IsActive' => $this->IsActive,
        //     'StartingPrice'=>$this->StartingPrice,
        //     'Packages' => $this->packages, 
        // ];
         return parent::toArray($request);
    }
}
