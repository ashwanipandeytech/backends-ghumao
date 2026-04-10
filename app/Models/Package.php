<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table='Package';
    protected $primaryKey = 'PackageId';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [

         'Type', 'Code','CategoryId', 'Title','PackageSlug','StartDate','Programs','Cancellation','NoOfDays', 'Description','Image', 'Inclusions','Exclusions','Price','Priority','IsActive','CreatedBy','NumberOfPerson','Terms','PackageName','ShortDescription','InnerImage','SliderImage','Note','isMenu'

    ];
    
   public function images()
   {
     return $this->hasMany('App\Image', 'package_id');
   }
}
