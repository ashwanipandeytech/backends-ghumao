<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packageimages extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='Package_Images';
    protected $fillable = [

          'Image','ImageType','package_id'
    ];

    public function package()
    {
      return $this->belongsTo('App\Models\Package', 'package_id');
    }

}
